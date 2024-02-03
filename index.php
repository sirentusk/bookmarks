<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Siren Watcher's Bookmarks</title>
    <script src="bookmarks.js"></script>
    <link rel="stylesheet" type="text/css" href="bookmarks.css"> 
</head>
    
<body>
    <header>
        <img src="bookmarks.webp">
    </header>
      
    <form method="post" action="bookmarks.php" enctype="multipart/form-data">
    <label for="url">URL:</label>
    <input type="url" id="url" name="url" required><br><br>

    <label for="tags">Tags:</label>
    <select id="tags" name="tags[]" multiple required>
        <!-- Options for tags can be dynamically populated from the database -->
        <option value="tag1">Tag 1</option>
        <option value="tag2">Tag 2</option>
        <!-- Add more options as needed -->
    </select><br><br>

    <label for="authors">Authors:</label>
    <select id="authors" name="authors[]" multiple required>
        <!-- Options for authors can be dynamically populated from the database -->
        <option value="author1">Author 1</option>
        <option value="author2">Author 2</option>
        <!-- Add more options as needed -->
    </select><br><br>

    <label for="release">Release:</label>
    <input type="date" id="release" name="release" value="<?php echo date('Y-m-d'); ?>"><br><br>

    <label for="created">Created:</label>
    <input type="date" id="created" name="created" value="<?php echo date('Y-m-d'); ?>"><br><br>

    <label for="alternative_url">Alternative URL:</label>
    <input type="url" id="alternative_url" name="alternative_url"><br><br>

    <label for="timestamp_quotes">Timestamp / Quotes:</label>
    <input type="text" id="timestamp_quotes" name="timestamp_quotes"><br><br>

    <label for="hours">Hours:</label>
    <input type="text" id="hours" name="hours"><br><br>

    <label for="finished">Finished:</label>
    <input type="date" id="finished" name="finished" value="<?php echo date('Y-m-d'); ?>"><br><br>

    <label for="episode">Episode:</label>
    <input type="text" id="episode" name="episode"><br><br>

    <label for="channel">Channel:</label>
    <select id="channel" name="channel[]" multiple>
        <!-- Options for channels can be dynamically populated from the database -->
        <option value="channel1">Channel 1</option>
        <option value="channel2">Channel 2</option>
        <!-- Add more options as needed -->
    </select><br><br>

    <label for="comment_review">Comment / Review:</label>
    <textarea id="comment_review" name="comment_review"></textarea><br><br>

    <label for="archive1">Archive 1:</label>
    <input type="url" id="archive1" name="archive1"><br><br>

    <label for="archive2">Archive 2:</label>
    <input type="url" id="archive2" name="archive2"><br><br>

    <button type="submit">Submit</button>
</form>

</body>
</html>
