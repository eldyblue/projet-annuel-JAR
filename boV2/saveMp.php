<?php
session_start();
require "lib.php";

if (isset($_POST['jsVerif'])){
    $pseudo = $_POST['pseudo'];
    if(!verifyPresent("UTILISATEURS", "pseudo", $pseudo)){
        echo "Destinataire non trouvé";
    }

} else {

    $user = isConnected();

    if (!empty($_POST)
        && isset($_POST["destinataire_mp"])
        && isset($_POST["sujet"])
        && isset($_POST["contenu_mp"])
    ) {

        $error = false;
        $listOfError = array();

        $sujet = trim($_POST["sujet"]);
        if (strlen($sujet) > 50 || strlen($sujet) < 3) {
            $error = true;
            $listOfError[] = 5;
        }

        $contenu_mp = trim($_POST["contenu_mp"]);
        if (strlen($contenu_mp) > 100 || strlen($contenu_mp) < 0) {
            $error = true;
            $listOfError[] = 6;
        }

        //=====================================================================//
        if (!$error) {
            $db = dbConnect();

                $query = $db->prepare("SELECT id_utilisateur FROM UTILISATEURS WHERE pseudo=:pseudo AND is_deleted=0");
                $query->execute([
                    "pseudo"=>$_POST['destinataire_mp']
                ]);
                $res = $query->fetch();

                //Traitement de le image

                //$myImage = uploadImage($_FILES["pj_mp"], "mp");

            $query = $db->prepare("INSERT INTO MESSAGESP (sujet, contenu_mp, auteur_mp, destinataire_mp) VALUES(:sujet, :contenu_mp, :auteur_mp, :destinataire_mp)");

            //=====================================================================//
            $query->execute([
                'sujet' => $sujet,
                "contenu_mp" => $contenu_mp,
                "auteur_mp" => $user['id_utilisateur'],
                "destinataire_mp" => $res['id_utilisateur']
                ]);
            header("location: mp.php");

        } //=====================================================================//
        else {//($error == true)
            header("location: mp.php?errors=" . implode(',', $listOfError));
        }
    } //=====================================================================//
    else {//($_POST est incorrect)
        header("location: tricheur.php");
    }
}