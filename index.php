<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
    <style>
        body {
            margin: 0;
            font-family: Verdana, Arial, sans-serif;
        }
        p {
            padding: 10px;
            margin: 5px;
            margin-left: 20px;
            border: 1px solid black;
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
        .post {
            margin-left: 20px;
            margin-right: 20px;
            margin-bottom: 10px;
        }
        .post-title {
            color: #003366;
            margin-bottom: 5px;
        }
        .comment {
            margin-left: 40px;
            margin-right: 20px;
            margin-bottom: 10px;
        }
        .comment-author {
            color: #666;
            font-weight: bold;
        }
        .connection-status {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 5px;
            margin: 5px 20px;
            font-size: 12px;
        }
        hr {
            border: 0;
            height: 1px;
            background-color: #ccc;
            margin: 20px;
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
    <h1 class="site-title">Facebook&copy; 2&trade;</h1>
    <?php
        $servername = "localhost";
        $username = "jaunaisusers";
        $password = "password";
        $dbname = "blog_12032025";
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo '<div class="connection-status">Facebook&copy; 2&trade; IZNĀCA, EJ GALĪGI</div>';
            $stmt = $conn->prepare(
                "SELECT posts.post_id, posts.title, posts.content, posts.author, posts.created_at,
                        comments.comment_id, comments.author AS comment_author, comments.content AS comment_content, comments.created_at AS comment_created_at
                FROM posts
                LEFT JOIN comments ON posts.post_id = comments.post_id
                ORDER BY posts.created_at DESC, comments.created_at ASC"
            );
            $stmt->execute();
            $current_post_id = null;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($current_post_id != $row['post_id']) {
                    if ($current_post_id !== null) {
                        echo "<hr>";
                    }
   
                    $current_post_id = $row['post_id'];
   
                    printf(
                        '<div class="post"><h2 class="post-title">(%s) %s</h2><p>%s</p><p>by <i>%s</i>, %s</p></div>',
                        $row["post_id"],
                        $row["title"],
                        $row["content"],
                        $row["author"],
                        $row["created_at"]
                    );
                }
   
                // If there's a comment, display it
                if ($row['comment_id']) {
                    printf(
                        '<div class="comment"><span class="comment-author">Kommentārs by %s</span><p>%s</p><p><i>On: %s</i></p></div>',
                        $row["comment_author"],
                        $row["comment_content"],
                        $row["comment_created_at"]
                    );
                }
            }
        } catch (PDOException $e) {
            echo '<div style="color: red; margin: 20px;">Connection failed: ' . $e->getMessage() . '</div>';
        }
        // Close the connection
        $conn = null;
    ?>
    <div class="copyright" style="text-align: center; margin-top: 20px; margin-bottom: 15px; font-size: 12px; color: #666;">
        &copy; 2025 Kārlis Rīders (Marka Cukerberga dēls)
    </div>
</body>
</html>