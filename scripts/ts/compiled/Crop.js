"use strict";
// ##################################### HTML STRUCTURE ###############################################
// <div id="crop1" class="crop">
//     <form action="imageUploader.php" method="POST" enctype="multipart/form-data" class="crop__form">
//         <input type="hidden" name="ProfilePictureSubmit" value="">
//         <button type="button" class="crop__submit">CROP</button>
//     </form>
//     <input type="file" class="crop__input" accept="image/*" name="crop-input" id="">
//     <div class="crop__cropper-container">
//         <img class="crop__cropper-image" data-src="" alt="">
//         <img class="crop__cropper-image-clipped" alt="">
//         <div class="crop__cropper">
//             <span class="crop__crophandle--tl"></span>
//             <span class="crop__crophandle--tr"></span>
//             <span class="crop__crophandle--br"></span>
//             <span class="crop__crophandle--bl"></span>
//         </div>
//         <div class="crop__crop-overlay"></div>
//     </div>
// </div>
// ####################################################################################################
var CropperBox = /** @class */ (function () {
    function CropperBox(element) {
        this.ar = 0;
        this.el = element;
        this.width = this.el.clientWidth;
        this.height = this.el.clientHeight;
        this.updateAspectRatio();
    }
    CropperBox.prototype.updateAspectRatio = function () {
        this.width = this.el.clientWidth;
        this.height = this.el.clientHeight;
        this.ar = this.width / this.height;
    };
    return CropperBox;
}());
var CropperImage = /** @class */ (function () {
    function CropperImage(imageIn, imageClip, cropperBox, onload) {
        var _this = this;
        this.pos = {};
        this.imageWidth = 0;
        this.imageHeight = 0;
        this.width = 0;
        this.height = 0;
        this.ar = 0;
        this.loaded = false;
        this.el = imageIn;
        this.el.crossOrigin = "anonymous";
        this.imageClip = imageClip;
        this.el.onload = function () {
            _this.imageWidth = imageIn.width;
            _this.imageHeight = imageIn.height;
            _this.imageClip.onload = function () {
                _this.loaded = true;
                _this.ar = _this.imageWidth / _this.imageHeight;
                _this.resize(cropperBox);
                _this.updateTransform();
                onload();
            };
            imageClip.src = _this.el.dataset.src;
        };
        imageIn.src = this.el.dataset.src;
    }
    CropperImage.prototype.resize = function (cropperBox) {
        // image wider than cropperBox
        if (this.ar > cropperBox.ar) {
            this.el.style.width = 100 + "%";
            this.el.style.height = "unset";
        }
        else {
            this.el.style.height = 100 + "%";
            this.el.style.width = "unset";
        }
        this.el.style.left = Math.floor(cropperBox.width / 2 - this.el.offsetWidth / 2) + "px";
        this.el.style.top = Math.floor(cropperBox.height / 2 - this.el.offsetHeight / 2) + "px";
        this.updateTransform();
    };
    CropperImage.prototype.updateImageClip = function (cropper) {
        this.imageClip.style.clipPath =
            "inset(" + (cropper.pos.rel.top - this.pos.rel.top) + "px \n            " + (cropper.pos.rel.right - this.pos.rel.right) + "px \n            " + (cropper.pos.rel.bottom - this.pos.rel.bottom) + "px \n            " + (cropper.pos.rel.left - this.pos.rel.left) + "px)";
        this.imageClip.style.top = this.pos.rel.top + "px";
        this.imageClip.style.left = this.pos.rel.left + "px";
        this.imageClip.style.width = this.width + "px";
        this.imageClip.style.height = this.height + "px";
    };
    CropperImage.prototype.updateTransform = function () {
        var rect = this.el.getBoundingClientRect();
        this.width = rect.width;
        this.height = rect.height;
        this.pos = {
            abs: {
                top: rect.top + window.scrollY,
                right: window.innerWidth - rect.right + window.scrollX,
                bottom: window.innerHeight - rect.bottom + window.scrollY,
                left: rect.left + window.scrollX
            },
            rel: {
                top: this.el.offsetTop,
                right: Number(window.getComputedStyle(this.el).right.slice(0, -2)),
                bottom: Number(window.getComputedStyle(this.el).bottom.slice(0, -2)),
                left: this.el.offsetLeft
            }
        };
    };
    return CropperImage;
}());
var Cropper = /** @class */ (function () {
    function Cropper(element) {
        var _this = this;
        this.ar = 0;
        this.pos = {};
        this.clickCursorPosAbs = { x: 0, y: 0 };
        this.clickCropperPosRel = { x: 0, y: 0 };
        this.width = 0; // WITHOUT border (or is it?!?!?)
        this.height = 0; // WITHOUT border (it's probably with border)
        this.minHeight = 100;
        this.minWidth = 100;
        this.el = element;
        this.borderWidth = Number(getComputedStyle(this.el).getPropertyValue('border-left-width').slice(0, -2));
        this.el.addEventListener("pointerdown", function (cursor) {
            _this.clicked = true;
            _this.clickCursorPosAbs = {
                x: cursor.clientX,
                y: cursor.clientY
            };
            _this.updateTransform();
            _this.clickCropperPosRel = {
                x: _this.pos.rel.left,
                y: _this.pos.rel.top
            };
            _this.updateAspectRatio();
        });
        window.addEventListener("pointerup", function () {
            _this.el.style.top = _this.pos.rel.top + "px";
            _this.el.style.left = _this.pos.rel.left + "px";
            _this.el.style.bottom = "unset";
            _this.el.style.right = "unset";
            _this.clicked = false;
        });
        this.updateTransform();
        this.updateAspectRatio();
        this.clicked = false;
    }
    Cropper.prototype.updateTransform = function () {
        var rect = this.el.getBoundingClientRect();
        this.width = rect.width;
        this.height = rect.height;
        this.pos = {
            abs: {
                top: rect.top + window.scrollY,
                right: window.innerWidth - rect.right + window.scrollX,
                bottom: window.innerHeight - rect.bottom + window.scrollY,
                left: rect.left + window.scrollX
            },
            rel: {
                top: this.el.offsetTop,
                right: Number(window.getComputedStyle(this.el).right.slice(0, -2)),
                bottom: Number(window.getComputedStyle(this.el).bottom.slice(0, -2)),
                left: this.el.offsetLeft
            }
        };
    };
    Cropper.prototype.updateAspectRatio = function () {
        this.ar = this.width / this.height;
    };
    Cropper.prototype.resize = function (cropperImage) {
        this.minWidth = cropperImage.width / 10;
        this.minHeight = cropperImage.height / 10;
        if (cropperImage.ar > 1) {
            this.el.style.height = cropperImage.height / 2 + "px";
            this.el.style.width = (cropperImage.height / 2) * this.ar + "px";
        }
        else {
            this.el.style.width = cropperImage.width / 2 + "px";
            this.el.style.height = (cropperImage.width / 2) * 1 / this.ar + "px";
        }
        this.el.style.left = cropperImage.pos.rel.left + "px";
        this.el.style.top = cropperImage.pos.rel.top + "px";
        this.updateTransform();
        this.updateAspectRatio();
        cropperImage.updateImageClip(this);
    };
    Cropper.prototype.move = function (cursor, cropperImage) {
        var next = {
            x: this.clickCropperPosRel.x + cursor.clientX - this.clickCursorPosAbs.x,
            y: this.clickCropperPosRel.y + cursor.clientY - this.clickCursorPosAbs.y
        };
        if (next.x < cropperImage.pos.rel.left) {
            this.clickCursorPosAbs.x = cursor.clientX;
            this.clickCropperPosRel.x = this.pos.rel.left;
            next.x = cropperImage.pos.rel.left;
        }
        else if (next.x + this.width - cropperImage.width > cropperImage.pos.rel.right) {
            this.clickCursorPosAbs.x = cursor.clientX;
            this.clickCropperPosRel.x = this.pos.rel.left;
            next.x = cropperImage.pos.rel.left + cropperImage.width - this.width;
        }
        if (next.y < cropperImage.pos.rel.top) {
            this.clickCursorPosAbs.y = cursor.clientY;
            this.clickCropperPosRel.y = this.pos.rel.top;
            next.y = cropperImage.pos.rel.top;
        }
        else if (next.y + this.height - cropperImage.height > cropperImage.pos.rel.bottom) {
            this.clickCursorPosAbs.y = cursor.clientY;
            this.clickCropperPosRel.y = this.pos.rel.top;
            next.y = cropperImage.pos.rel.top + cropperImage.height - this.height;
        }
        this.el.style.left = next.x + 'px';
        this.el.style.top = next.y + 'px';
        this.updateTransform();
    };
    Cropper.prototype.transformCropper = function (handle, cropperImage, cursor) {
        var next = {
            w: 0,
            h: 0
        };
        switch (handle.type) {
            case HandleType.TL:
                next = {
                    w: this.width - (cursor.clientX - this.clickCursorPosAbs.x),
                    h: this.height - (cursor.clientY - this.clickCursorPosAbs.y)
                };
                this.el.style.top = "unset";
                this.el.style.right = this.pos.rel.right + "px";
                this.el.style.bottom = this.pos.rel.bottom + "px";
                this.el.style.left = "unset";
                if (next.w < this.minWidth)
                    next.w = this.minWidth;
                if (next.h + this.pos.rel.bottom >= cropperImage.height + cropperImage.pos.rel.bottom) {
                    next.h = cropperImage.height - (this.pos.rel.bottom - cropperImage.pos.rel.bottom);
                    this.el.style.height = next.h + "px";
                    this.el.style.width = Number(window.getComputedStyle(this.el).height.slice(0, -2)) * this.ar + "px";
                    break;
                }
                else if (next.w + this.pos.rel.right >= cropperImage.width + cropperImage.pos.rel.right)
                    next.w = cropperImage.width - (this.pos.rel.right - cropperImage.pos.rel.right);
                this.el.style.width = next.w + "px";
                this.el.style.height = Number(window.getComputedStyle(this.el).width.slice(0, -2)) * 1 / this.ar + "px";
                break;
            case HandleType.TR:
                next = {
                    w: this.width + cursor.clientX - this.clickCursorPosAbs.x,
                    h: this.height - (cursor.clientY - this.clickCursorPosAbs.y)
                };
                this.el.style.top = "unset";
                this.el.style.right = "unset";
                this.el.style.bottom = this.pos.rel.bottom + "px";
                this.el.style.left = this.pos.rel.left + "px";
                if (next.w < this.minWidth)
                    next.w = this.minWidth;
                if (next.h + this.pos.rel.bottom >= cropperImage.height + cropperImage.pos.rel.bottom) {
                    next.h = cropperImage.height - (this.pos.rel.bottom - cropperImage.pos.rel.bottom);
                    this.el.style.height = next.h + "px";
                    this.el.style.width = Number(window.getComputedStyle(this.el).height.slice(0, -2)) * this.ar + "px";
                    break;
                }
                else if (next.w + this.pos.rel.left >= cropperImage.width + cropperImage.pos.rel.left)
                    next.w = cropperImage.width - (this.pos.rel.left - cropperImage.pos.rel.left);
                this.el.style.width = next.w + "px";
                this.el.style.height = Number(window.getComputedStyle(this.el).width.slice(0, -2)) * 1 / this.ar + "px";
                break;
            case HandleType.BL:
                next = {
                    w: this.width - (cursor.clientX - this.clickCursorPosAbs.x),
                    h: this.height + cursor.clientY - this.clickCursorPosAbs.y
                };
                this.el.style.top = this.pos.rel.top + "px";
                this.el.style.right = this.pos.rel.right + "px";
                this.el.style.bottom = "unset";
                this.el.style.left = "unset";
                if (next.w < this.minWidth)
                    next.w = this.minWidth;
                if (next.h + this.pos.rel.top >= cropperImage.height + cropperImage.pos.rel.top) {
                    next.h = cropperImage.height - (this.pos.rel.top - cropperImage.pos.rel.top);
                    this.el.style.height = next.h + "px";
                    this.el.style.width = Number(window.getComputedStyle(this.el).height.slice(0, -2)) * this.ar + "px";
                    break;
                }
                else if (next.w + this.pos.rel.right >= cropperImage.width + cropperImage.pos.rel.right)
                    next.w = cropperImage.width - (this.pos.rel.right - cropperImage.pos.rel.right);
                this.el.style.width = next.w + "px";
                this.el.style.height = Number(window.getComputedStyle(this.el).width.slice(0, -2)) * 1 / this.ar + "px";
                break;
            case HandleType.BR:
                next = {
                    w: this.width + cursor.clientX - this.clickCursorPosAbs.x,
                    h: this.height + cursor.clientY - this.clickCursorPosAbs.y
                };
                this.el.style.top = this.pos.rel.top + "px";
                this.el.style.right = "unset";
                this.el.style.bottom = "unset";
                this.el.style.left = this.pos.rel.left + "px";
                if (next.w < this.minWidth)
                    next.w = this.minWidth;
                if (next.h + this.pos.rel.top >= cropperImage.height + cropperImage.pos.rel.top) {
                    next.h = cropperImage.height - (this.pos.rel.top - cropperImage.pos.rel.top);
                    this.el.style.height = next.h + "px";
                    this.el.style.width = Number(window.getComputedStyle(this.el).height.slice(0, -2)) * this.ar + "px";
                    break;
                }
                else if (next.w + this.pos.rel.left >= cropperImage.width + cropperImage.pos.rel.left)
                    next.w = cropperImage.width - (this.pos.rel.left - cropperImage.pos.rel.left);
                this.el.style.width = next.w + "px";
                this.el.style.height = Number(window.getComputedStyle(this.el).width.slice(0, -2)) * 1 / this.ar + "px";
                break;
        }
        this.clickCursorPosAbs = {
            x: cursor.clientX,
            y: cursor.clientY
        };
        this.updateTransform();
    };
    return Cropper;
}());
var HandleType;
(function (HandleType) {
    HandleType["TL"] = "TL";
    HandleType["TR"] = "TR";
    HandleType["BL"] = "BL";
    HandleType["BR"] = "BR";
})(HandleType || (HandleType = {}));
var CropperHandles = /** @class */ (function () {
    function CropperHandles(cropperId, cropper) {
        var _this = this;
        var tl = { el: document.querySelector("#" + cropperId + " .crop__crophandle--tl"), clicked: false, type: HandleType.TL };
        var tr = { el: document.querySelector("#" + cropperId + " .crop__crophandle--tr"), clicked: false, type: HandleType.TR };
        var br = { el: document.querySelector("#" + cropperId + " .crop__crophandle--br"), clicked: false, type: HandleType.BR };
        var bl = { el: document.querySelector("#" + cropperId + " .crop__crophandle--bl"), clicked: false, type: HandleType.BL };
        this.handles = Array(tl, tr, br, bl);
        var _loop_1 = function (handle) {
            handle.el.addEventListener("pointerdown", function (cursor) {
                cursor.preventDefault();
                handle.clicked = true;
                cropper.clickCursorPosAbs = {
                    x: cursor.clientX,
                    y: cursor.clientY
                };
                cropper.updateTransform();
                cropper.clickCropperPosRel = {
                    x: cropper.pos.rel.left,
                    y: cropper.pos.rel.top
                };
                cursor.stopPropagation();
            });
        };
        for (var _i = 0, _a = this.handles; _i < _a.length; _i++) {
            var handle = _a[_i];
            _loop_1(handle);
        }
        window.addEventListener("pointerup", function () {
            for (var _i = 0, _a = _this.handles; _i < _a.length; _i++) {
                var handle = _a[_i];
                handle.clicked = false;
            }
        });
    }
    return CropperHandles;
}());
var Crop = /** @class */ (function () {
    function Crop(crop, ar) {
        var _this = this;
        this.cropperHandles = {};
        var input = document.querySelector("#" + crop.id + " .crop__input");
        var submit = document.querySelector("#" + crop.id + " .crop__submit");
        this.form = document.querySelector("#" + crop.id + " .crop__form");
        submit.disabled = true;
        submit.addEventListener("click", function () {
            var cropped = _this.submitCropped();
            document.body.append(cropped);
        });
        this.cropper = new Cropper(document.querySelector("#" + crop.id + " .crop__cropper"));
        this.cropper.el.style.display = "none";
        document.querySelector("#" + crop.id + " .crop__cropper-container").style.display = "none";
        input.addEventListener("change", function () {
            if (input.value != "") {
                document.querySelector("#" + crop.id + " .crop__cropper-container").style.display = "block";
                _this.cropperBox = new CropperBox(document.querySelector("#" + crop.id + " .crop__cropper-container"));
                crop.dataset.src = URL.createObjectURL(input.files[0]);
                document.querySelector("#" + crop.id + " .crop__cropper-image").dataset.src = URL.createObjectURL(input.files[0]);
                _this.cropperImage = new CropperImage(document.querySelector("#" + crop.id + " .crop__cropper-image"), document.querySelector("#" + crop.id + " .crop__cropper-image-clipped"), _this.cropperBox, function () {
                    submit.disabled = false;
                    _this.setAspectRatio(ar);
                    _this.cropper.el.style.display = "block";
                    _this.cropper.resize(_this.cropperImage);
                    _this.cropperHandles = new CropperHandles(crop.id, _this.cropper);
                    _this.initUpdate();
                });
            }
        });
    }
    Crop.prototype.initUpdate = function () {
        var _this = this;
        document.addEventListener("pointermove", function (cursor) {
            if (_this.cropper.clicked) {
                _this.cropper.move(cursor, _this.cropperImage);
                _this.cropperImage.updateImageClip(_this.cropper);
            }
            for (var _i = 0, _a = _this.cropperHandles.handles; _i < _a.length; _i++) {
                var handle = _a[_i];
                if (handle.clicked) {
                    _this.cropper.transformCropper(handle, _this.cropperImage, cursor);
                    _this.cropperImage.updateImageClip(_this.cropper);
                }
            }
        });
        window.addEventListener("resize", function () {
            _this.cropperBox.updateAspectRatio();
            _this.cropperImage.resize(_this.cropperBox);
            _this.cropper.resize(_this.cropperImage);
        });
    };
    Crop.prototype.setAspectRatio = function (ar) {
        this.cropper.ar = ar;
        if (this.cropperImage.loaded)
            this.cropper.resize(this.cropperImage);
    };
    Crop.prototype.submitCropped = function () {
        var _this = this;
        // create a canvas that will present the output image
        var imageOut = document.createElement("canvas");
        // set it to the same size as the image
        imageOut.width = this.cropperImage.imageWidth * this.cropper.width / this.cropperImage.width;
        imageOut.height = this.cropperImage.imageHeight * this.cropper.height / this.cropperImage.height;
        var ctx = imageOut.getContext("2d");
        ctx.drawImage(this.cropperImage.el, -(this.cropper.pos.rel.left - this.cropperImage.pos.rel.left) * this.cropperImage.imageWidth / this.cropperImage.el.width, -(this.cropper.pos.rel.top - this.cropperImage.pos.rel.top) * this.cropperImage.imageHeight / this.cropperImage.el.height);
        imageOut.toBlob(function (blob) {
            var formDataImage = document.createElement("input");
            formDataImage.name = "file";
            formDataImage.type = "file";
            var container = new DataTransfer();
            container.items.add(new File([blob], "img.jpeg", { type: "image/jpeg", lastModified: new Date().getTime() }));
            console.log();
            formDataImage.files = container.files;
            _this.form.append(formDataImage);
            _this.form.submit();
        }, 'image/jpeg', 1.0);
        return imageOut;
    };
    return Crop;
}());
