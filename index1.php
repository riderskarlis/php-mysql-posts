<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog with Comment Pagination</title>
    <style>
        p {
            padding: 10px;
            margin: 5px;
            margin-left: 20px;
            border: 1px solid black;
        }
        .comments-container {
            margin-left: 40px;
        }
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }
        .pagination li {
            margin: 0 5px;
            cursor: pointer;
        }
        .pagination li.active {
            font-weight: bold;
        }
        .pagination-container {
            margin: 10px 0 20px 40px;
        }
        .hidden {
            display: none;
        }
        .comment {
            margin-bottom: 10px;
        }
        .load-more {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 5px 10px;
            cursor: pointer;
            margin-left: 40px;
            display: inline-block;
        }
    </style>
</head>
<body>

<?php
$servername = "localhost";
$username = "jaunaisusers";
$password = "password";
$dbname = "blog_12032025";
$comments_per_page = 1; // Number of comments to show per page

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully<br>";
    
    // Get all posts
    $stmt = $conn->prepare(
        "SELECT post_id, title, content, author, created_at FROM posts ORDER BY created_at DESC"
    );
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($posts as $post) {
        printf(
            "(%s) <h2>%s</h2><p>%s</p><p>by <i>%s</i>, %s</p>",
            $post["post_id"],
            $post["title"],
            $post["content"],
            $post["author"],
            $post["created_at"]
        );
        
        // Get comment count for this post
        $comment_count_stmt = $conn->prepare(
            "SELECT COUNT(*) as count FROM comments WHERE post_id = :post_id"
        );
        $comment_count_stmt->bindParam(':post_id', $post["post_id"]);
        $comment_count_stmt->execute();
        $comment_count = $comment_count_stmt->fetch(PDO::FETCH_ASSOC)["count"];
        
        // Get comments for this post (first page only)
        $comments_stmt = $conn->prepare(
            "SELECT comment_id, author, content, created_at FROM comments 
             WHERE post_id = :post_id 
             ORDER BY created_at ASC 
             LIMIT :limit"
        );
        $comments_stmt->bindParam(':post_id', $post["post_id"]);
        $comments_stmt->bindValue(':limit', $comments_per_page, PDO::PARAM_INT);
        $comments_stmt->execute();
        $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<div class='comments-container' id='comments-container-" . $post["post_id"] . "'>";
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
        echo "</div>";
        
        // Calculate number of pages
        $pages = ceil($comment_count / $comments_per_page);
        
        if ($pages > 1) {
            echo "<div class='pagination-container' id='pagination-" . $post["post_id"] . "'>";
            echo "<ul class='pagination'>";
            for ($i = 1; $i <= $pages; $i++) {
                $active_class = ($i == 1) ? "active" : "";
                echo "<li class='page-item $active_class' data-page='$i' data-post-id='" . $post["post_id"] . "'>$i</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
        
        echo "<hr><br>";
    }
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$conn = null;
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click event to pagination items
    document.querySelectorAll('.page-item').forEach(item => {
        item.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const page = this.getAttribute('data-page');
            loadComments(postId, page);
            
            // Update active class
            document.querySelectorAll(`#pagination-${postId} .page-item`).forEach(pageItem => {
                pageItem.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
});

function loadComments(postId, page) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `get_comments.php?post_id=${postId}&page=${page}`, true);
    
    xhr.onload = function() {
        if (this.status === 200) {
            document.getElementById(`comments-container-${postId}`).innerHTML = this.responseText;
        }
    };
    
    xhr.send();
}
</script>

</body>
</html>