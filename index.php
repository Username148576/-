<html>
  <head>
    <script src="https://unpkg.com/konva@8.3.13/konva.min.js"></script>
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"
      integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/"
      crossorigin="anonymous"
    ></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"></script>
    <meta charset="utf-8" />
    <title>圖片編輯器</title>
    <style>
      body {
        margin: 0;
        padding: 50;
        overflow: hidden;
        background-color: #747474;
      }
    </style>
  </head>
  <body>
    <h1>圖片編輯器</h1>
    <input type="file" id="file-uploader" data-target="file-uploader" accept="image/*"/>
    <button id="text">加入文字</button>
    <button id="transformer">縮放/旋轉</button>
    <button id="move">移動</button>
    <form method="post" action="upload.php">
    <input type="hidden" name="imagestring">
    <input type="button" value="儲存圖片" id="save">
    <input type="submit" value="確認上傳" id="upload" disabled=true/>
    <br />
    <img id="show_image" src="">
    </form>
    <div id="container"></div>
    <script>
      width=640;
      height=400;
      var stage = new Konva.Stage({
        container: 'container',
        width: width,
        height: height,
      });

      var layer = new Konva.Layer();

      var stageRect =  new Konva.Rect({ 
        x:0,
        y:0,
        width: width,
        height: height,
        fill: 'white'
      })
      layer.add(stageRect);

      stageRect.draw();

      stage.add(layer);

      //stage.container().style.backgroundColor='white';

  // create shape
  var b=0, b1=0;

  //document.getElementById('upload').disabled=true;

  function drawImage(imageObj) {
        // darth vader
        var Img = new Konva.Image({
          image: imageObj,
          x: stage.width() / 2 - 200 / 2,
          y: stage.height() / 2 - 137 / 2,
          width: 200,
          height: 137,
          draggable: true,
        });
        layer.add(Img);

        var tr = new Konva.Transformer();
        layer.add(tr);
        tr.nodes([Img]);

        document.getElementById('transformer').addEventListener(
          'click',
          function () {
            tr.show();
        });
        document.getElementById('move').addEventListener(
          'click',
          function () {
            tr.hide();
        });
        document.getElementById('save').addEventListener(
          'click',
          function () {
            tr.hide();
        });
      }

      const fileUploader = document.querySelector('#file-uploader');

      fileUploader.addEventListener('change', (e) => {
        console.log(e.target.files[0]);
        const curFile = e.target.files[0];
        const objectURL = URL.createObjectURL(curFile);
        var imageObj = new Image();
        imageObj.src = objectURL;
        imageObj.onload = function () {
          drawImage(this);
        };
      });

      document.getElementById('text').addEventListener(
        'click',
        function () {
      var textNode = new Konva.Text({
        text: 'Some text here',
        x: 50,
        y: 80,
        fontSize: 20,
        draggable: true,
        width: 200,
      });

      layer.add(textNode);

      var tr = new Konva.Transformer({
        node: textNode,
        enabledAnchors: ['middle-left', 'middle-right'],
        // set minimum width of text
        boundBoxFunc: function (oldBox, newBox) {
          newBox.width = Math.max(30, newBox.width);
          return newBox;
        },
      });

      textNode.on('transform', function () {
        // reset scale, so only with is changing by transformer
        textNode.setAttrs({
          width: textNode.width() * textNode.scaleX(),
          scaleX: 1,
        });
      });

      layer.add(tr);

      document.getElementById('transformer').addEventListener(
          'click',
          function () {
            tr.show();
        });
        document.getElementById('move').addEventListener(
          'click',
          function () {
            tr.hide();
        });
        document.getElementById('save').addEventListener(
          'click',
          function () {
            tr.hide();
        });

      textNode.on('dblclick dbltap', () => {
        // hide text node and transformer:
        textNode.hide();
        tr.hide();
        var textPosition = textNode.absolutePosition();
        var areaPosition = {
          x: stage.container().offsetLeft + textPosition.x,
          y: stage.container().offsetTop + textPosition.y,
        };
        var textarea = document.createElement('textarea');
        document.body.appendChild(textarea);
        textarea.value = textNode.text();
        textarea.style.position = 'absolute';
        textarea.style.top = areaPosition.y + 'px';
        textarea.style.left = areaPosition.x + 'px';
        textarea.style.width = textNode.width() - textNode.padding() * 2 + 'px';
        textarea.style.height =
          textNode.height() - textNode.padding() * 2 + 5 + 'px';
        textarea.style.fontSize = textNode.fontSize() + 'px';
        textarea.style.border = 'none';
        textarea.style.padding = '0px';
        textarea.style.margin = '0px';
        textarea.style.overflow = 'hidden';
        textarea.style.background = 'none';
        textarea.style.outline = 'none';
        textarea.style.resize = 'none';
        textarea.style.lineHeight = textNode.lineHeight();
        textarea.style.fontFamily = textNode.fontFamily();
        textarea.style.transformOrigin = 'left top';
        textarea.style.textAlign = textNode.align();
        textarea.style.color = textNode.fill();
        rotation = textNode.rotation();
        var transform = '';
        if (rotation) {
          transform += 'rotateZ(' + rotation + 'deg)';
        }

        var px = 0;
        var isFirefox =
          navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
        if (isFirefox) {
          px += 2 + Math.round(textNode.fontSize() / 20);
        }
        transform += 'translateY(-' + px + 'px)';

        textarea.style.transform = transform;

        // reset height
        textarea.style.height = 'auto';
        // after browsers resized it we can set actual value
        textarea.style.height = textarea.scrollHeight + 3 + 'px';

        textarea.focus();

        function removeTextarea() {
          textarea.parentNode.removeChild(textarea);
          window.removeEventListener('click', handleOutsideClick);
          textNode.show();
          tr.show();
          tr.forceUpdate();
        }

        function setTextareaWidth(newWidth) {
          if (!newWidth) {
            // set width for placeholder
            newWidth = textNode.placeholder.length * textNode.fontSize();
          }
          // some extra fixes on different browsers
          var isSafari = /^((?!chrome|android).)*safari/i.test(
            navigator.userAgent
          );
          var isFirefox =
            navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
          if (isSafari || isFirefox) {
            newWidth = Math.ceil(newWidth);
          }

          var isEdge =
            document.documentMode || /Edge/.test(navigator.userAgent);
          if (isEdge) {
            newWidth += 1;
          }
          textarea.style.width = newWidth + 'px';
        }

        textarea.addEventListener('keydown', function (e) {
          // hide on enter
          // but don't hide on shift + enter
          if (e.keyCode === 13 && !e.shiftKey) {
            textNode.text(textarea.value);
            removeTextarea();
          }
          // on esc do not set value back to node
          if (e.keyCode === 27) {
            removeTextarea();
          }
        });

        textarea.addEventListener('keydown', function (e) {
          scale = textNode.getAbsoluteScale().x;
          setTextareaWidth(textNode.width() * scale);
          textarea.style.height = 'auto';
          textarea.style.height =
            textarea.scrollHeight + textNode.fontSize() + 'px';
        });

        function handleOutsideClick(e) {
          if (e.target !== textarea) {
            textNode.text(textarea.value);
            removeTextarea();
          }
        }
        setTimeout(() => {
          window.addEventListener('click', handleOutsideClick);
        });
      });    
        }
      );

      document.getElementById('save').addEventListener('click', function () {
        var dataURL = stage.toDataURL({ pixelRatio: 3 });
        // 將 DataURL 放到表單中
        $("input[name='imagestring']").val(dataURL);
        document.getElementById('upload').disabled=false;
      });
    </script>
  </body>
</html>


