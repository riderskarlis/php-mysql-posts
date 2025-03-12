<!DOCTYPE html>
<html>
<head>
    <title>Add New Post</title>
    <style>
        body {
            font-family: Verdana, Arial, sans-serif;
            margin: 0;
        }
        h1 {
            color: navy;
        }
        form {
            margin: 15px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input, textarea {
            width: 400px;
            border: 1px solid #999;
            margin-bottom: 10px;
        }
        textarea {
            height: 150px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        button {
            background-color: #ccc;
            border: 2px outset #ccc;
            padding: 3px 8px;
            margin-top: 10px;
        }
        .navbar {
            background-color: #003366;
            width: 100%;
            padding: 5px 0;
            margin-bottom: 15px;
        }
        .navbar table {
            width: 100%;
            border-collapse: collapse;
        }
        .navbar td {
            text-align: center;
        }
        .navbar a {
            color: white;
            font-family: Verdana, Arial, sans-serif;
            font-size: 12px;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 15px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .site-title {
            font-family: "Times New Roman", Times, serif;
            color: #003366;
            margin: 5px 0 15px 0;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <table>
            <tr>
                <td><a href="index.php">Galvenā lapa&trade;</a></td>
                <td><a href="new-post.php">Uzrakstīt jauno postu&trade;</a></td>
                <td><a href="delete-post.php">Izdzēst postu&trade;</a></td>
            </tr>
        </table>
    </div>
    
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $servername = "localhost";
        $username = "jaunaisusers";
        $password = "password";
        $dbname = "blog_12032025";
        
        $title = $_POST['title'];
        $content = $_POST['content'];
        $author = $_POST['author'];
        
        if (empty($title) || empty($content) || empty($author)) {
            echo '<p class="error">Please fill in all fields</p>';
        } else {
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $conn->prepare("INSERT INTO posts (title, content, author, created_at) VALUES (:title, :content, :author, NOW())");
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':content', $content);
                $stmt->bindParam(':author', $author);
                $stmt->execute();
                
                echo '<p class="success">Post added successfully!</p>';
                $title = $content = $author = "";
            } catch (PDOException $e) {
                echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
            }
            $conn = null;
        }
    }
    ?>
    
    <form method="POST">
        <h1 class="site-title">Add New Post</h1>

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>">
        
        <label for="content">Content:</label>
        <textarea id="content" name="content"><?php echo isset($content) ? htmlspecialchars($content) : ''; ?></textarea>
        
        <label for="author">Author:</label>
        <input type="text" id="author" name="author" value="<?php echo isset($author) ? htmlspecialchars($author) : ''; ?>">
        
        <br>
        <button type="submit">Add Post</button>
        <br>
        <p><a href="index.php">Back to Blog</a></p>
    </form>
    <div class="copyright" style="text-align: center; margin-top: 20px; margin-bottom: 15px; font-size: 12px; color: #666;">
        &copy; 2025 Kārlis Rīders (Marka Cukerberga dēls)
    </div>
    
</body>
</html>