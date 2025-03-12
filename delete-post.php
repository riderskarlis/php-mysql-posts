<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Post</title>
    <style>
        body {
            margin: 0;
            font-family: Verdana, Arial, sans-serif;
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
            text-align: center;
        }
        .form-container {
            margin: 0 20px;
        }
        select, button {
            border: 1px solid #999;
            background-color: #eee;
            padding: 3px;
            margin: 5px 0;
        }
        button {
            background-color: #ccc;
            border: 2px outset #ccc;
            padding: 3px 8px;
            margin-top: 10px;
            cursor: pointer;
        }
        .success {
            color: green;
            font-weight: bold;
            margin: 10px 0;
        }
        .error {
            color: red;
            font-weight: bold;
            margin: 10px 0;
        }
        .post-preview {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }
        .confirm-box {
            border: 2px solid #ff0000;
            padding: 10px;
            margin: 10px 0;
            background-color: #fff0f0;
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
    <h1 class="site-title">Delete Post</h1>
    
    <div class="form-container">
    <?php
        $servername = "localhost";
        $username = "jaunaisusers";
        $password = "password";
        $dbname = "blog_12032025";
        
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Process delete request
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
                $post_id = $_POST['post_id'];
                
                // First delete all comments associated with the post
                $delete_comments = $conn->prepare("DELETE FROM comments WHERE post_id = :post_id");
                $delete_comments->bindParam(':post_id', $post_id);
                $delete_comments->execute();
                
                // Then delete the post
                $delete_post = $conn->prepare("DELETE FROM posts WHERE post_id = :post_id");
                $delete_post->bindParam(':post_id', $post_id);
                $delete_post->execute();
                
                echo "<p class='success'>Post ID $post_id and all associated comments have been deleted.</p>";
            }
            
            // Show confirmation form
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id']) && !isset($_POST['confirm_delete'])) {
                $post_id = $_POST['post_id'];
                
                // Get post details for confirmation
                $get_post = $conn->prepare("SELECT title, author, created_at FROM posts WHERE post_id = :post_id");
                $get_post->bindParam(':post_id', $post_id);
                $get_post->execute();
                $post = $get_post->fetch(PDO::FETCH_ASSOC);
                
                if ($post) {
                    echo "<div class='confirm-box'>";
                    echo "<h3>Are you sure you want to delete this post?</h3>";
                    echo "<div class='post-preview'>";
                    echo "<p><strong>Title:</strong> " . htmlspecialchars($post['title']) . "</p>";
                    echo "<p><strong>Author:</strong> " . htmlspecialchars($post['author']) . "</p>";
                    echo "<p><strong>Created:</strong> " . htmlspecialchars($post['created_at']) . "</p>";
                    echo "</div>";
                    echo "<p><strong>Warning:</strong> This will also delete all comments associated with this post.</p>";
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='post_id' value='$post_id'>";
                    echo "<input type='hidden' name='confirm_delete' value='yes'>";
                    echo "<button type='submit'>Yes, Delete Post</button> ";
                    echo "<a href='delete-post.php'>Cancel</a>";
                    echo "</form>";
                    echo "</div>";
                } else {
                    echo "<p class='error'>Post not found.</p>";
                }
            } else {
                // Get all posts for selection
                $get_posts = $conn->prepare("SELECT post_id, title, author, created_at FROM posts ORDER BY created_at DESC");
                $get_posts->execute();
                $posts = $get_posts->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($posts) > 0) {
                    echo "<form method='POST'>";
                    echo "<label for='post_id'>Select post to delete:</label><br>";
                    echo "<select name='post_id' id='post_id'>";
                    foreach ($posts as $post) {
                        echo "<option value='" . $post['post_id'] . "'>" . 
                             htmlspecialchars($post['title']) . " (by " . 
                             htmlspecialchars($post['author']) . ", " . 
                             $post['created_at'] . ")</option>";
                    }
                    echo "</select><br>";
                    echo "<button type='submit'>Select Post</button>";
                    echo "</form>";
                } else {
                    echo "<p>No posts found.</p>";
                }
            }
        } catch (PDOException $e) {
            echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
        }
        
        // Close the connection
        $conn = null;
    ?>
    </div>
    <div class="copyright" style="text-align: center; margin-top: 20px; margin-bottom: 15px; font-size: 12px; color: #666;">
        &copy; 2025 Kārlis Rīders (Marka Cukerberga dēls)
    </div>
</body>
</html>