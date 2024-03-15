<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Extraction Result</title>
    <style>
        .highlight {
            background-color: yellow;
        }
    </style>
</head>
<body>
    <form id="searchForm">
        <input type="text" id="searchInput" placeholder="Enter words to search">
        <button type="button" id="searchButton">Search</button>
        <span>Enter words to search (separated by space)</span>
    </form>
    
    <div id="textContainer">
        <p>{{ $text }}</p>
    </div>

    <script>
        document.getElementById("searchButton").addEventListener("click", function() {
            var searchInput = document.getElementById("searchInput").value.trim();
            var searchWords = searchInput.split(/\s+/).filter(Boolean);
            var textContainer = document.getElementById("textContainer");
            var text = textContainer.innerHTML;

            textContainer.innerHTML = text.replace(/<span class="highlight">(.*?)<\/span>/g, "$1");

            searchWords.forEach(function(searchWord) {
                var regex = new RegExp(searchWord, 'gi');
                textContainer.innerHTML = textContainer.innerHTML.replace(regex, function(match) {
                    return '<span class="highlight">' + match + '</span>';
                });
            });
        });
    </script>
</body>
</html>
