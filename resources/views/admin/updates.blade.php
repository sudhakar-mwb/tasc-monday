<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="d-flex flex-column border-bottom py-3 p-10" style="gap:20px;">
    <div class="fw-bold" style="text-align:start;font-size:17px;color:#6F7490;">Documents</div>
    <div></div>
    <div>
    <button class="btn btn-to-link btn-secondary btn-gradiant  d-flex align-items-center justify-content-around" type="button">
    <span> Updates</span>
    <span class="icon-btn_track" style="height: 22px;width: 22px">
    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" height="20px" style="max-width: 100%;"><path d="M0,50c0,27.612,22.388,50,50.012,50C77.612,100,100,77.612,100,50c0-27.618-22.388-50-49.988-50  C22.388,0,0,22.382,0,50z M75,49.902H56.25V75h-12.5V49.902H25.098l24.914-24.908L75,49.902z" fill="currentColor"></path></svg></span>
    </button>
    </div>

    </div>

    <div id="quill-editor" class="mb-3" style="min-height: 60px;"></div>
   <textarea rows="3" class="mb-3 d-none" name="description" id="quill-editor-area"></textarea>
   <script>
let  modules = {
    toolbar: [
      [{ size: ["small", false, "large", "huge"] }],
      ["bold", "italic", "underline", "strike", "blockquote"],
      [{ list: "ordered" }, { list: "bullet" }],
      ["link"],
      [
        { list: "ordered" },
        { list: "bullet" },
        { indent: "-1" },
        { indent: "+1" },
        { align: [] },
      ],
    ],
  };

  var formats = [
    "header",
    "height",
    "bold",
    "italic",
    "underline",
    "strike",
    "blockquote",
    "list",
    "bullet",
    "indent",
    "link",
    "image",
    "align",
    "size",
  ];

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('quill-editor-area')) {
        var editor = new Quill('#quill-editor', {
            theme: 'snow',
            modules: modules,
            format: formats
        });
        var quillEditor = document.getElementById('quill-editor-area');
        editor.on('text-change', function() {
            quillEditor.value = editor.root.innerHTML;
        });

        quillEditor.addEventListener('input', function() {
            editor.root.innerHTML = quillEditor.value;
        });
    }
});
</script>
</body>
</html>