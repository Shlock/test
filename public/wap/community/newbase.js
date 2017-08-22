$(function ()
{
    //页面跳转加载显示器 固定.pageloading
    $(document).on("touchend", '.pageloading', function ()
    {
        // $.showIndicator();
        // setTimeout(function(){
        //     $.hideIndicator();
        //     $.toast("加载超时");
        // },'30000');
    });

    //查看密码 固定.eye
    $(document).on("touchend", '.eye', function ()
    {
        var obj = $(this).parent().find('input')
        var type = obj.attr("type");
        if (type == "password")
        {
            obj.attr("type", "text");
        }
        if (type == "text")
        {
            obj.attr("type", "password");
        }
    });

    //返回按钮
    $.back = function(msg, title)
    {
        if ( typeof(msg) == 'undefined' ){
            $.router.back();
        }
        else {
            $.confirm(msg, typeof(title) == 'undefined' ? '操作提示' : title, function(){
                $.router.back();
            }) 
        }
    }

    //点击跳转
    $.href = function(url)
    {
        $.showIndicator();
        window.location.href = url;
        setTimeout(function(){
            $.hideIndicator();
            $.toast("加载超时");
        },'30000');
    }

    function setCookie(c_name, value, expiredays)
    {
        var exdate = new Date()
        exdate.setDate(exdate.getDate() + expiredays)
        document.cookie = c_name + "=" + escape(value) + ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString())
    }

    // 是否app
    if (window.App)
    {
        setCookie("app", "true", 1);
    }

    //back操作
    $(document).off('click','.back');
    $(document).on('click','.back', function (event) {
        if (window.history.length == 1) {
            $.router.loadPage(this.href);
            event.preventDefault();
            return false;
        }
    });

    // 添加'refresh'监听器
    // $(document).on('refresh', '.pull-to-refresh-content',function(e) {
    //     window.location.reload();
    // });
    
})

// jpg信息读取 zgb 2012-3-18
function ExifReader(file)
{
    var TiffTags =
        {
            0x0100: "ImageWidth",
            0x0101: "ImageHeight",
            0x8769: "ExifIFDPointer",
            0x8825: "GPSInfoIFDPointer",
            0xA005: "InteroperabilityIFDPointer",
            0x0102: "BitsPerSample",
            0x0103: "Compression",
            0x0106: "PhotometricInterpretation",
            0x0112: "Orientation",
            0x0115: "SamplesPerPixel",
            0x011C: "PlanarConfiguration",
            0x0212: "YCbCrSubSampling",
            0x0213: "YCbCrPositioning",
            0x011A: "XResolution",
            0x011B: "YResolution",
            0x0128: "ResolutionUnit",
            0x0111: "StripOffsets",
            0x0116: "RowsPerStrip",
            0x0117: "StripByteCounts",
            0x0201: "JPEGInterchangeFormat",
            0x0202: "JPEGInterchangeFormatLength",
            0x012D: "TransferFunction",
            0x013E: "WhitePoint",
            0x013F: "PrimaryChromaticities",
            0x0211: "YCbCrCoefficients",
            0x0214: "ReferenceBlackWhite",
            0x0132: "DateTime",
            0x010E: "ImageDescription",
            0x010F: "Make",
            0x0110: "Model",
            0x0131: "Software",
            0x013B: "Artist",
            0x8298: "Copyright"
        };

    function getStringFromDB(buffer, start, length)
    {
        var outstr = "";

        for (n = start; n < start + length; n++)
        {
            outstr += String.fromCharCode(buffer.getUint8(n));
        }

        return outstr;
    }
    function readEXIFData(file, start)
    {
        if (getStringFromDB(file, start, 4) != "Exif")
        {
            return null;
        }

        var bigEnd,
            tags, tag,
            exifData, gpsData,
            tiffOffset = start + 6;

        // test for TIFF validity and endianness
        if (file.getUint16(tiffOffset) == 0x4949)
        {
            bigEnd = false;
        }
        else if (file.getUint16(tiffOffset) == 0x4D4D)
        {
            bigEnd = true;
        }
        else
        {
            return null;
        }

        if (file.getUint16(tiffOffset + 2, !bigEnd) != 0x002A)
        {
            return null;
        }

        var firstIFDOffset = file.getUint32(tiffOffset + 4, !bigEnd);

        if (firstIFDOffset < 0x00000008)
        {
            return null;
        }

        return readTags(file, tiffOffset, tiffOffset + firstIFDOffset, TiffTags, bigEnd);
    }
    function readTags(file, tiffStart, dirStart, strings, bigEnd)
    {
        var entries = file.getUint16(dirStart, !bigEnd),
            tags = {},
            entryOffset, tag,
            i;

        for (i = 0; i < entries; i++)
        {
            entryOffset = dirStart + i * 12 + 2;

            tag = strings[file.getUint16(entryOffset, !bigEnd)];

            tags[tag] = readTagValue(file, entryOffset, tiffStart, dirStart, bigEnd);
        }

        return tags;
    }
    function readTagValue(file, entryOffset, tiffStart, dirStart, bigEnd)
    {
        var type = file.getUint16(entryOffset + 2, !bigEnd),
            numValues = file.getUint32(entryOffset + 4, !bigEnd),
            valueOffset = file.getUint32(entryOffset + 8, !bigEnd) + tiffStart,
            offset,
            vals, val, n,
            numerator, denominator;

        switch (type)
        {
            case 1: // byte, 8-bit unsigned int
            case 7: // undefined, 8-bit byte, value depending on field
                if (numValues == 1)
                {
                    return file.getUint8(entryOffset + 8, !bigEnd);
                }
                else
                {
                    offset = numValues > 4 ? valueOffset : (entryOffset + 8);
                    vals = [];
                    for (n = 0; n < numValues; n++)
                    {
                        vals[n] = file.getUint8(offset + n);
                    }
                    return vals;
                }

            case 2: // ascii, 8-bit byte
                offset = numValues > 4 ? valueOffset : (entryOffset + 8);
                return getStringFromDB(file, offset, numValues - 1);

            case 3: // short, 16 bit int
                if (numValues == 1)
                {
                    return file.getUint16(entryOffset + 8, !bigEnd);
                }
                else
                {
                    offset = numValues > 2 ? valueOffset : (entryOffset + 8);
                    vals = [];
                    for (n = 0; n < numValues; n++)
                    {
                        vals[n] = file.getUint16(offset + 2 * n, !bigEnd);
                    }
                    return vals;
                }

            case 4: // long, 32 bit int
                if (numValues == 1)
                {
                    return file.getUint32(entryOffset + 8, !bigEnd);
                }
                else
                {
                    vals = [];
                    for (n = 0; n < numValues; n++)
                    {
                        vals[n] = file.getUint32(valueOffset + 4 * n, !bigEnd);
                    }
                    return vals;
                }

            case 5:    // rational = two long values, first is numerator, second is denominator
                if (numValues == 1)
                {
                    numerator = file.getUint32(valueOffset, !bigEnd);
                    denominator = file.getUint32(valueOffset + 4, !bigEnd);
                    val = new Number(numerator / denominator);
                    val.numerator = numerator;
                    val.denominator = denominator;
                    return val;
                }
                else
                {
                    vals = [];
                    for (n = 0; n < numValues; n++)
                    {
                        numerator = file.getUint32(valueOffset + 8 * n, !bigEnd);
                        denominator = file.getUint32(valueOffset + 4 + 8 * n, !bigEnd);
                        vals[n] = new Number(numerator / denominator);
                        vals[n].numerator = numerator;
                        vals[n].denominator = denominator;
                    }
                    return vals;
                }

            case 9: // slong, 32 bit signed int
                if (numValues == 1)
                {
                    return file.getInt32(entryOffset + 8, !bigEnd);
                }
                else
                {
                    vals = [];
                    for (n = 0; n < numValues; n++)
                    {
                        vals[n] = file.getInt32(valueOffset + 4 * n, !bigEnd);
                    }
                    return vals;
                }

            case 10: // signed rational, two slongs, first is numerator, second is denominator
                if (numValues == 1)
                {
                    return file.getInt32(valueOffset, !bigEnd) / file.getInt32(valueOffset + 4, !bigEnd);
                }
                else
                {
                    vals = [];
                    for (n = 0; n < numValues; n++)
                    {
                        vals[n] = file.getInt32(valueOffset + 8 * n, !bigEnd) / file.getInt32(valueOffset + 4 + 8 * n, !bigEnd);
                    }
                    return vals;
                }
        }
    }

    var dataView = new DataView(file);

    if ((dataView.getUint8(0) != 0xFF) ||
        (dataView.getUint8(1) != 0xD8))
    {
        return;
    }

    var offset = 2,
        length = file.byteLength,
        marker;

    while (offset < length)
    {
        if (dataView.getUint8(offset) != 0xFF)
        {
            return null;
        }

        marker = dataView.getUint8(offset + 1);

        if (marker == 225)
        {
            return readEXIFData(dataView, offset + 4, dataView.getUint16(offset + 2) - 2);
        }
        else
        {
            offset += 2 + dataView.getUint16(offset + 2);
        }
    }
}
// 解决ios图片问题 zgb 2012-3-18
function MegaPixImage(srcImage, options)
{
    /**
    * Detect subsampling in loaded image.
    * In iOS, larger images than 2M pixels may be subsampled in rendering.
    */
    function detectSubsampling(img)
    {
        var iw = img.naturalWidth, ih = img.naturalHeight;
        if (iw * ih > 1024 * 1024)
        { // subsampling may happen over megapixel image
            var canvas = document.createElement('canvas');
            canvas.width = canvas.height = 1;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, -iw + 1, 0);
            // subsampled image becomes half smaller in rendering size.
            // check alpha channel value to confirm image is covering edge pixel or not.
            // if alpha value is 0 image is not covering, hence subsampled.
            return ctx.getImageData(0, 0, 1, 1).data[3] === 0;
        }
        else
        {
            return false;
        }
    }
    /**
    * Detecting vertical squash in loaded image.
    * Fixes a bug which squash image vertically while drawing into canvas for some images.
    */
    function detectVerticalSquash(img, iw, ih)
    {
        var canvas = document.createElement('canvas');
        canvas.width = 1;
        canvas.height = ih;
        var ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        var data = ctx.getImageData(0, 0, 1, ih).data;
        // search image edge pixel position in case it is squashed vertically.
        var sy = 0;
        var ey = ih;
        var py = ih;
        while (py > sy)
        {
            var alpha = data[(py - 1) * 4 + 3];
            if (alpha === 0)
            {
                ey = py;
            } else
            {
                sy = py;
            }
            py = (ey + sy) >> 1;
        }
        var ratio = (py / ih);
        return (ratio === 0) ? 1 : ratio;
    }
    /**
   * Rendering image element (with resizing) and get its data URL
   */
    function renderImageToDataURL(img, options, doSquash)
    {
        var canvas = document.createElement("canvas");
        renderImageToCanvas(img, canvas, options, doSquash);
        return canvas.toDataURL("image/png");
    }
    /**
    * Rendering image element (with resizing) into the canvas element
    */
    function renderImageToCanvas(img, canvas, options, doSquash)
    {
        var iw = img.naturalWidth, ih = img.naturalHeight;
        var width = options.width, height = options.height;
        var ctx = canvas.getContext('2d');
        
        ctx.save();
        transformCoordinate(canvas, ctx, width, height, options.orientation);
        
        var subsampled = detectSubsampling(img);
        if (subsampled)
        {
            iw /= 2;
            ih /= 2;
        }
        var d = 1024; // size of tiling canvas
        var tmpCanvas = document.createElement('canvas');
        tmpCanvas.width = tmpCanvas.height = d;
        var tmpCtx = tmpCanvas.getContext('2d');
        var vertSquashRatio = doSquash ? detectVerticalSquash(img, iw, ih) : 1;
        var dw = Math.ceil(d * width / iw);
        var dh = Math.ceil(d * height / ih / vertSquashRatio);
        var sy = 0;
        var dy = 0;
        while (sy < ih)
        {
            var sx = 0;
            var dx = 0;
            while (sx < iw)
            {
                tmpCtx.clearRect(0, 0, d, d);
                tmpCtx.drawImage(img, -sx, -sy);
                ctx.drawImage(tmpCanvas, 0, 0, d, d, dx, dy, dw, dh);
                sx += d;
                dx += dw;
            }
            sy += d;
            dy += dh;
        }
        ctx.restore();
        tmpCanvas = tmpCtx = null;
    }

    /**
    * Transform canvas coordination according to specified frame size and orientation
    * Orientation value is from EXIF tag
    */
    function transformCoordinate(canvas, ctx, width, height, orientation)
    {
        switch (orientation)
        {
            case 5:
            case 6:
            case 7:
            case 8:
                canvas.width = height;
                canvas.height = width;
                break;
            default:
                canvas.width = width;
                canvas.height = height;
        }
        switch (orientation)
        {
            case 2:
                // horizontal flip
                ctx.translate(width, 0);
                ctx.scale(-1, 1);
                break;
            case 3:
                // 180 rotate left
                ctx.translate(width, height);
                ctx.rotate(Math.PI);
                break;
            case 4:
                // vertical flip
                ctx.translate(0, height);
                ctx.scale(1, -1);
                break;
            case 5:
                // vertical flip + 90 rotate right
                ctx.rotate(0.5 * Math.PI);
                ctx.scale(1, -1);
                break;
            case 6:
                // 90 rotate right
                ctx.rotate(0.5 * Math.PI);
                ctx.translate(0, -height);
                break;
            case 7:
                // horizontal flip + 90 rotate right
                ctx.rotate(0.5 * Math.PI);
                ctx.translate(width, -height);
                ctx.scale(-1, 1);
                break;
            case 8:
                // 90 rotate left
                ctx.rotate(-0.5 * Math.PI);
                ctx.translate(-width, 0);
                break;
            default:
                break;
        }
    }

    options = options || {};
    var imgWidth = srcImage.naturalWidth,
        imgHeight = srcImage.naturalHeight,
        width = options.width,
        height = options.height,
        maxWidth = options.maxWidth,
        maxHeight = options.maxHeight,
        doSquash = false;

    if (width && !height)
    {
        height = (imgHeight * width / imgWidth) << 0;
    }
    else if (height && !width)
    {
        width = (imgWidth * height / imgHeight) << 0;
    }
    else
    {
        width = imgWidth;
        height = imgHeight;
    }

    if (maxWidth && width > maxWidth)
    {
        width = maxWidth;
        height = (imgHeight * width / imgWidth) << 0;
    }

    if (maxHeight && height > maxHeight)
    {
        height = maxHeight;
        width = (imgWidth * height / imgHeight) << 0;
    }

    var opt = { width: width, height: height };

    for (var k in options) opt[k] = options[k];

    return renderImageToDataURL(srcImage, opt, doSquash);
}
// 图片上传 zgb 2012-3-18
function PhotoCutUpload(sender, width, height, url, callback, style, show)
{
    if (sender.files.length == 0) return;

    var reader = new FileReader();

    reader.onload = function ()
    {
        var tags = ExifReader(this.result);

        tags = tags ? tags : { Orientation: 1 };

        var reader = new FileReader();

        var MAX_SIZE_ZOOM = 3;

        if (show === false)
        {
            MAX_SIZE_ZOOM = 2; // 压缩比高一点
        }

        tags = tags ? tags : { Orientation: 1 };

        reader.onload = function ()
        {
            var img = new Image();

            img.onload = function ()
            {
                var src = "";

                if (img.naturalWidth > width * MAX_SIZE_ZOOM ||
                    img.naturalHeight > height * MAX_SIZE_ZOOM)
                {
                    var zoom = 1;

                    if (width * MAX_SIZE_ZOOM / img.naturalWidth < height * MAX_SIZE_ZOOM / img.naturalHeight)
                    {
                        zoom = width * MAX_SIZE_ZOOM / img.naturalWidth;
                    }
                    else
                    {
                        zoom = height * MAX_SIZE_ZOOM / img.naturalHeight;
                    }

                    src = MegaPixImage(img, { width: img.naturalWidth * zoom, height: img.naturalHeight * zoom, orientation: tags.Orientation });
                }
                else
                {
                    src = img.src;
                }

                // 不进行裁剪，只进行压缩
                if (show === false)
                {
                    $.post(url,
                        {
                            file: src
                        },
                        function (result)
                        {
                            if (typeof (callback) == "function")
                            {
                                callback(result);
                            }
                        },
                        "json");

                    return;
                }

                function onClick()
                {
                    var left = parseFloat(imgFile.style.left),
                        top = parseFloat(imgFile.style.top);

                    if (isNaN(left)) left = 0;
                    if (isNaN(top)) top = 0;

                    var options =
                        {
                            sx: Math.max(0, -left),
                            sy: Math.max(0, -top),
                            swidth: Math.max(0, Math.min(width, imgFile.naturalWidth, imgFile.naturalWidth + left, width - left)),
                            sheight: Math.max(0, Math.min(height, imgFile.naturalHeight, imgFile.naturalHeight + top, height - top)),
                            x: Math.max(0, left),
                            y: Math.max(0, top),
                            width: 0,
                            height: 0,
                            cwidth: width,
                            cheight: height
                        };

                    options.width = Math.max(0, Math.min(width - options.x, options.swidth));
                    options.height = Math.max(0, Math.min(height - options.y, options.sheight));

                    function cutImageToDataURL(img, options)
                    {
                        var canvas = document.createElement('canvas');

                        canvas.width = options.cwidth;
                        canvas.height = options.cheight;

                        var ctx = canvas.getContext('2d');

                        ctx.fillStyle = "#ffffff";
                        ctx.fillRect(0, 0, canvas.width, canvas.height);

                        ctx.drawImage(img,
                            options.sx,
                            options.sy,
                            options.swidth,
                            options.sheight,
                            options.x,
                            options.y,
                            options.width,
                            options.height);

                        return canvas.toDataURL("image/png");
                    }

                    $.post(url,
                        {
                            file: cutImageToDataURL(imgFile, options)
                        },
                        function (result)
                        {
                            if (typeof (callback) == "function")
                            {
                                callback(result);
                            }
                        },
                        "json");
                }

                var modal = $.modal(
                {
                    text: "<div class='cutphoto' style='width:" + width + "px;height:" + height + "px;'></div>",
                    title: "裁剪图片",
                    extraClass: style,
                    buttons: [
                        { text: "取消" },
                        { text: "裁剪", bold: true, onClick: onClick }
                    ]
                });

                var divBox = $(".cutphoto", modal);

                var imgFile = document.createElement("img");

                imgFile.style.position = "relative";
                imgFile.src = src;

                divBox.append(imgFile);

                var moveOptions = { x1: 0, y1: 0, x2: 0, y2: 0 };

                $(document).on("touchstart", ".cutphoto", function (event)
                {
                    event.preventDefault();
                    event.stopPropagation();

                    moveOptions =
                        {
                            x1: event.changedTouches[0].pageX,
                            y1: event.changedTouches[0].pageY,
                            x2: 0,
                            y2: 0
                        };
                });

                $(document).on("touchmove", ".cutphoto", function (event)
                {
                    event.preventDefault();
                    event.stopPropagation();

                    moveOptions.x2 = event.changedTouches[0].pageX;
                    moveOptions.y2 = event.changedTouches[0].pageY;

                    var left = parseFloat(imgFile.style.left),
                        top = parseFloat(imgFile.style.top);

                    if (isNaN(left)) left = 0;
                    if (isNaN(top)) top = 0;

                    imgFile.style.left = (left + moveOptions.x2 - moveOptions.x1) + "px";
                    imgFile.style.top = (top + moveOptions.y2 - moveOptions.y1) + "px";

                    moveOptions.x1 = moveOptions.x2;
                    moveOptions.y1 = moveOptions.y2;
                });
            }

            img.src = this.result;
        }

        reader.readAsDataURL(sender.files[0]);

        sender.form.reset();
    }

    reader.readAsArrayBuffer(sender.files[0]);
}