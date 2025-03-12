<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        p {
            padding: 10px;
            margin: 5px;
            margin-left: 20px;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <?php
        $servername = "localhost";
        $username = "jaunaisusers";
        $password = "password";
        $dbname = "blog_12032025";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "Connected successfully<br>";

            $stmt = $conn->prepare(
                "SELECT posts.post_id, posts.title, posts.content, posts.author, posts.created_at,
                        comments.comment_id, comments.author AS comment_author, comments.content AS comment_content, comments.created_at AS comment_created_at
                FROM posts
                LEFT JOIN comments ON posts.post_id = comments.post_id
                ORDER BY posts.created_at DESC, comments.created_at ASC"
            );
            $stmt->execute();

            // Initialize $current_post_id to ensure the first comparison works
            $current_post_id = null;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Check if it's a new post
                if ($current_post_id != $row['post_id']) {
                    // If not the first post, print a separator
                    if ($current_post_id !== null) {
                        echo "<hr><br>";
                    }
    
                    // Update the current post ID
                    $current_post_id = $row['post_id'];
    
                    // Display the post
                    printf(
                        "(%s) <h2>%s</h2><p>%s</p><p>by <i>%s</i>, %s</p><br>",
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
                        "<div style='margin-left: 20px;'><b>Comment by %s</b><br><p>%s</p><p><i>On: %s</i></p></div><br>",
                        $row["comment_author"],
                        $row["comment_content"],
                        $row["comment_created_at"]
                    );
                }
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        // Close the connection
        $conn = null;
    ?>
</body>
</html>
