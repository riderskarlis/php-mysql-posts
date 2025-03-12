<?php
$servername = "localhost";
$username = "jaunaisusers";
$password = "password";
$dbname = "blog_12032025";
$comments_per_page = 1; // Same as in the main file

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if(isset($_GET['post_id']) && isset($_GET['page'])) {
        $post_id = $_GET['post_id'];
        $page = $_GET['page'];
        
        // Calculate offset
        $offset = ($page - 1) * $comments_per_page;
        
        // Get comments for this post and page
        $comments_stmt = $conn->prepare(
            "SELECT comment_id, author, content, created_at FROM comments 
             WHERE post_id = :post_id 
             ORDER BY created_at ASC 
             LIMIT :limit OFFSET :offset"
        );
        $comments_stmt->bindParam(':post_id', $post_id);
        $comments_stmt->bindValue(':limit', $comments_per_page, PDO::PARAM_INT);
        $comments_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $comments_stmt->execute();
        $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Output comments
        foreach ($comments as $comment) {
            echo "<div class='comment'>";
            printf(
                "<b>Kommentārs by %s</b><br><p>%s</p><p><i>On: %s</i></p>",
                $comment["author"],
                $comment["content"],
                $comment["created_at"]
            );
            echo "</div>";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>