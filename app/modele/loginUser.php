<?php
include __DIR__ . '/user.php';

// Vérification de pseudo unique
function checkIfPseudoExist($pseudo)
{
    $conn = connectPDO();
    if (!$conn) {
        addError("Erreur de base de données : Connexion non établie.");
        return false;
    }
    try {
        $request = 'SELECT id_utilisateur FROM utilisateur WHERE pseudoU = :pseudo';
        $statement = $conn->prepare($request);
        $statement->execute(['pseudo' => $pseudo]);
        $user = $statement->fetch();
    } catch (PDOException $e) {
        addError("Erreur de base de donnée, veuillez réessayer ultérieurement.");
    }
    return $user !== false;
}

// connection de l'utilisateur/administrateur
// Connection de l'utilisateur/administrateur
function login($pseudo, $password)
{
    try {
        // Vérification si l'utilisateur existe
        if (!checkIfPseudoExist($pseudo)) {
            throw new Exception("Nom d'utilisateur non disponnible.");
        }

        // Récupération des informations de l'utilisateur
        $util = getUserByPseudo($pseudo);
        $mdpBD = $util['mdpU'];
        $role = $util['role'];
        $mail = $util['mailU'];
        $id = $util['id_utilisateur'];

        if (trim($mdpBD) == trim(crypt($password, $mdpBD))) {
            // Le mot de passe est celui de l'utilisateur dans la base de données
            $_SESSION["pseudo"] = $pseudo;
            $_SESSION["mail"] = $mail;
            $_SESSION["role"] = $role;
            $_SESSION["id"] = $id;
            return true;
        } else {
            throw new Exception("Mot de passe incorrect.");
        }
    } catch (Exception $e) {
        addError("Erreur de base de donnée, veuillez réessayer ultérieurement.");
        return false;
    }
}


