<?php
class database{
 
    function opencon(): PDO{
        return new PDO(
            dsn: 'mysql:host=localhost;
            dbname=lms_app',
            username: 'root',
            password: '');
    }
 
    function signupUser($firstname, $lastname, $birthday, $email, $sex, $phone, $username, $password, $profile_picture_path) {
 
        $con = $this->opencon();
       
        try {
            $con->beginTransaction();
 
            $stmt = $con->prepare("INSERT INTO users (user_FN, user_LN, user_birthday, user_sex, user_email, user_phone, user_username, user_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $birthday, $sex, $email, $phone, $username, $password]);
           
            $userID = $con->lastInsertId();
           
 
            $stmt = $con->prepare("INSERT INTO users_pictures (user_id, user_pic_url) VALUES (?, ?)");
            $stmt->execute([$userID, $profile_picture_path]);
 
            $con->commit();
 
            return $userID;
 
        } catch (PDOException $e) {
 
            $con->rollback();
            return false;
 
        }
 
    }
 
    function insertAddress($userID, $street, $barangay, $city, $province) {
 
        $con = $this->opencon();
 
        try {
            $con->beginTransaction();
 
            $stmt = $con->prepare("INSERT INTO Address (ba_street, ba_barangay, ba_city, ba_province) VALUES (?, ?, ?, ?)");
            $stmt->execute([$street, $barangay, $city, $province]);
 
            $addressID = $con->lastInsertId();
 
            $stmt = $con->prepare("INSERT INTO users_address (user_id, address_id) VALUES (?, ?)");
            $stmt->execute([$userID, $addressID]);
 
            $con->commit();
 
            return true;
 
        } catch (PDOException $e) {
 
            $con->rollback();
            return false;
 
        }
 
    }

    function loginUser($email, $password){

        $con = $this->opencon();

        $stmt = $con->prepare("SELECT * FROM users WHERE user_email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['user_password'])) {

            return $user;

        }else{

            return false;

        }

    }

    function viewAuthors()
    {
        $con = $this->opencon();
        return $con->query("SELECT * FROM Authors")->fetchAll();
    }   

    function viewAuthorsID($id)
    {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM Authors WHERE author_ID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function addAuthor($authorFN, $authorLN, $authorBD, $authorNat){
        $con = $this->opencon();
        try{
            $con->beginTransaction();

            $stmt = $con->prepare("INSERT INTO Authors (author_FN, author_LN, author_birthday, author_nat) VALUES (?, ?, ?, ?)");
            $stmt->execute([$authorFN, $authorLN, $authorBD, $authorNat]);

            $authorID = $con->lastInsertId();
            $con->commit();

            return $authorID;

        }catch (PDOException $e){
            $con->rollBack();
            return false;
        }

    }


    function updateAuthor($id, $fn, $ln, $birthdate, $nat) {
        $con = $this->opencon(); // Add this line to get the PDO connection
        $sql = "UPDATE Authors SET author_FN=?, author_LN=?, author_birthday=?, author_nat=? WHERE author_ID=?";
        $stmt = $con->prepare($sql); // Prepare the statement
        return $stmt->execute([$fn, $ln, $birthdate, $nat, $id]);
    }

    function updateUser($user_id, $firstname, $lastname, $birthday, $sex, $username, $password) {
    try {
        $con = $this->opencon();
        $con->beginTransaction();
        $query = $con->prepare("UPDATE users SET user_firstname=?, user_lastname=?,user_birthday=?, user_sex=?,user_name=?, user_pass=? WHERE user_id=?");
        $query->execute([$firstname, $lastname,$birthday,$sex,$username, $password, $user_id]);
        // Update successful
        $con->commit();
        return true;
    } catch (PDOException $e) {
        // Handle the exception (e.g., log error, return false, etc.)
         $con->rollBack();
        return false; // Update failed
    }
}

    function addGenre($genreName){
        $con = $this->opencon();
        try{
            $con->beginTransaction();

            $stmt = $con->prepare("INSERT INTO genres (genre_name) VALUES (?)");
            $stmt->execute([$genreName]);

            $genreID = $con->lastInsertId();
            $con->commit();

            return $genreID;

        }catch (PDOException $e){
            $con->rollBack();
            return false;
        }

    }

    function viewGenres()
    {
        $con = $this->opencon();
        return $con->query("SELECT * FROM Genres ORDER BY genre_id")->fetchAll();
    }   

    function viewGenresID($id)
    {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM Genres WHERE genre_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function updateGenre($id, $name) {
    $con = $this->opencon();
    $sql = "UPDATE Genres SET genre_name=? WHERE genre_id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$name, $id]);
}

function addBook($title, $isbn, $pubyear, $quantity, $genre_ids =[], $author_ids = []) {
    $con = $this->opencon();
    try {
        $con->beginTransaction();

        // Insert book into Books table
        $stmt = $con->prepare("INSERT INTO Books (book_title, book_isbn, book_pubyear, quantity_avail) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $isbn, $pubyear, $quantity]);
        $book_id = $con->lastInsertId();

        // Insert into Genre_Books table
        foreach ($genre_ids as $genre_id) {
            $stmt = $con->prepare("INSERT INTO Genre_Books (genre_id, book_id) VALUES (?, ?)");
            $stmt->execute([$genre_id, $book_id]);
        }

        // Insert into BookAuthors table
        foreach ($author_ids as $author_id) {
            $stmt = $con->prepare("INSERT INTO Book_Authors (book_id, author_id) VALUES (?, ?)");
            $stmt->execute([$book_id, $author_id]);
        }

        //insert into Book_Copy table for each quantity
        for ($i = 0; $i < $quantity; $i++) {
            $stmt = $con->prepare("INSERT INTO Book_Copy (book_id, is_available) VALUES (?, 1)");
            $stmt->execute([$book_id]);
        }

        $con->commit();
        return true;

    } catch (PDOException $e) {
        $con->rollBack();
        return false;
    }
}
}


?>
