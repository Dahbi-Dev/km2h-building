<!-- // admin/blog/includes/editor-header.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <style>
    .ql-editor {
        min-height: 200px;
    }

    .ql-container {
        font-size: 16px;
    }
    </style>
</head>

<body>

    <!-- // Modify create.php and edit.php to use the rich text editor -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize English editor
        const quillEN = new Quill('#editor-en', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        'header': [1, 2, 3, false]
                    }],
                    ['bold', 'italic', 'underline'],
                    ['link', 'blockquote'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['clean']
                ]
            }
        });

        // Initialize French editor
        const quillFR = new Quill('#editor-fr', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        'header': [1, 2, 3, false]
                    }],
                    ['bold', 'italic', 'underline'],
                    ['link', 'blockquote'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['clean']
                ]
            }
        });

        // Set initial content if editing
        const contentEN = document.querySelector('input[name="content"]').value;
        const contentFR = document.querySelector('input[name="content_fr"]').value;

        if (contentEN) {
            quillEN.root.innerHTML = contentEN;
        }
        if (contentFR) {
            quillFR.root.innerHTML = contentFR;
        }

        // Update hidden inputs before form submission
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelector('input[name="content"]').value = quillEN.root.innerHTML;
            document.querySelector('input[name="content_fr"]').value = quillFR.root.innerHTML;
        });
    });
    </script>

    // Replace textarea elements with Quill editors
    <div>
        <label class="block text-sm font-medium text-gray-700">Content</label>
        <div id="editor-en" class="mt-1"></div>
        <input type="hidden" name="content" value="<?php echo htmlspecialchars($post['content'] ?? ''); ?>">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Contenu</label>
        <div id="editor-fr" class="mt-1"></div>
        <input type="hidden" name="content_fr" value="<?php echo htmlspecialchars($post['content_fr'] ?? ''); ?>">
    </div>