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

type Position = {
    abs: {
        top: number,
        right: number,
        bottom: number,
        left: number
    }
    rel: {
        top: number,
        right: number,
        bottom: number,
        left: number
    }
}

interface ICropperBox {
    el: HTMLDivElement,
    width: number,
    height: number,
    ar: number,
}

class CropperBox implements ICropperBox {
    el: HTMLDivElement;
    width: number;
    height: number;
    ar = 0;
    constructor(element: HTMLDivElement) {
        this.el = element;
        this.width = this.el.clientWidth;
        this.height = this.el.clientHeight;
        this.updateAspectRatio();
    }
    public updateAspectRatio(): void {
        this.width = this.el.clientWidth;
        this.height = this.el.clientHeight;
        this.ar = this.width / this.height;
    }
}

interface ICropperImage {
    el: HTMLDivElement,
    imageClip: HTMLImageElement,
    pos: Position,
    imageWidth: number,
    imageHeight: number,
    width: number,
    height: number,
    ar: number,
    loaded: boolean,
}

class CropperImage implements ICropperImage {
    el: HTMLImageElement;
    imageClip: HTMLImageElement;
    pos = {} as Position;
    imageWidth = 0;
    imageHeight = 0;
    width = 0;
    height = 0;
    ar = 0;
    loaded = false;
    constructor(imageIn: HTMLImageElement, imageClip: HTMLImageElement, cropperBox: CropperBox, onload: () => void) {
        this.el = imageIn;
        this.el.crossOrigin = "anonymous";
        this.imageClip = imageClip;
        this.el.onload! = () => {
            this.imageWidth = imageIn.width;
            this.imageHeight = imageIn.height;
            this.imageClip.onload! = () => {
                this.loaded = true;
                this.ar = this.imageWidth / this.imageHeight;
                this.resize(cropperBox);
                this.updateTransform();
                onload();
            };
            imageClip.src = this.el.dataset.src!;
        };
        imageIn.src = this.el.dataset.src!;
    }

    public resize(cropperBox: CropperBox): void {
        // image wider than cropperBox
        if (this.ar > cropperBox.ar) {
            this.el.style.width = 100 + "%";
            this.el.style.height = "unset";
        } else {
            this.el.style.height = 100 + "%";
            this.el.style.width = "unset";
        }
        this.el.style.left = Math.floor(cropperBox.width / 2 - this.el.offsetWidth / 2) + "px";
        this.el.style.top = Math.floor(cropperBox.height / 2 - this.el.offsetHeight / 2) + "px";
        this.updateTransform();
    }

    public updateImageClip(cropper: Cropper) {
        this.imageClip.style.clipPath =
            `inset(${cropper.pos.rel.top - this.pos.rel.top}px 
            ${cropper.pos.rel.right - this.pos.rel.right}px 
            ${cropper.pos.rel.bottom - this.pos.rel.bottom}px 
            ${cropper.pos.rel.left - this.pos.rel.left}px)`;
        this.imageClip.style.top = this.pos.rel.top + "px";
        this.imageClip.style.left = this.pos.rel.left + "px";
        this.imageClip.style.width = this.width + "px";
        this.imageClip.style.height = this.height + "px";
    }

    public updateTransform(): void {
        const rect = this.el.getBoundingClientRect();
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
        }
    }
}

interface ICropper {
    el: HTMLDivElement
    pos: Position
    clickCursorPosAbs: { x: number, y: number }
    clickCropperPosRel: { x: number, y: number }
    width: number
    height: number
    minHeight: number
    minWidth: number
    ar: number
    clicked: boolean
    borderWidth: number
}

class Cropper implements ICropper {
    el: HTMLDivElement;
    ar = 0;
    pos = {} as Position;
    clickCursorPosAbs = { x: 0, y: 0 }
    clickCropperPosRel = { x: 0, y: 0 }
    width = 0;  // WITHOUT border (or is it?!?!?)
    height = 0; // WITHOUT border (it's probably with border)
    minHeight = 100;
    minWidth = 100;
    clicked: boolean;
    borderWidth: number;

    constructor(element: HTMLDivElement) {
        this.el = element;
        this.borderWidth = Number(getComputedStyle(this.el).getPropertyValue('border-left-width').slice(0, -2))
        this.el.addEventListener("pointerdown", (cursor) => {
            this.clicked = true;
            this.clickCursorPosAbs = {
                x: cursor.clientX,
                y: cursor.clientY
            }
            this.updateTransform();
            this.clickCropperPosRel = {
                x: this.pos.rel.left,
                y: this.pos.rel.top
            }
            this.updateAspectRatio();
        })

        window.addEventListener("pointerup", () => {
            this.el.style.top = this.pos.rel.top + "px";
            this.el.style.left = this.pos.rel.left + "px";
            this.el.style.bottom = "unset";
            this.el.style.right = "unset";
            this.clicked = false;
        })

        this.updateTransform();
        this.updateAspectRatio();
        this.clicked = false;
    }

    public updateTransform(): void {
        const rect = this.el.getBoundingClientRect();
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
        }
    }

    public updateAspectRatio(): void {
        this.ar = this.width / this.height;
    }

    public resize(cropperImage: CropperImage): void {
        this.minWidth = cropperImage.width / 10;
        this.minHeight = cropperImage.height / 10;
        if (cropperImage.ar > 1) {
            this.el.style.height = cropperImage.height / 2 + "px";
            this.el.style.width = (cropperImage.height / 2) * this.ar + "px";
        } else {
            this.el.style.width = cropperImage.width / 2 + "px";
            this.el.style.height = (cropperImage.width / 2) * 1 / this.ar + "px";
        }
        this.el.style.left = cropperImage.pos.rel.left + "px";
        this.el.style.top = cropperImage.pos.rel.top + "px";
        this.updateTransform();
        this.updateAspectRatio();
        cropperImage.updateImageClip(this);
    }

    public move(cursor: PointerEvent, cropperImage: CropperImage) {
        const next = {
            x: this.clickCropperPosRel.x + cursor.clientX - this.clickCursorPosAbs.x,
            y: this.clickCropperPosRel.y + cursor.clientY - this.clickCursorPosAbs.y
        }

        if (next.x < cropperImage.pos.rel.left) {
            this.clickCursorPosAbs.x = cursor.clientX;
            this.clickCropperPosRel.x = this.pos.rel.left;
            next.x = cropperImage.pos.rel.left;
        } else if (next.x + this.width - cropperImage.width > cropperImage.pos.rel.right) {
            this.clickCursorPosAbs.x = cursor.clientX;
            this.clickCropperPosRel.x = this.pos.rel.left;
            next.x = cropperImage.pos.rel.left + cropperImage.width - this.width;
        }

        if (next.y < cropperImage.pos.rel.top) {
            this.clickCursorPosAbs.y = cursor.clientY;
            this.clickCropperPosRel.y = this.pos.rel.top;
            next.y = cropperImage.pos.rel.top;
        } else if (next.y + this.height - cropperImage.height > cropperImage.pos.rel.bottom) {
            this.clickCursorPosAbs.y = cursor.clientY;
            this.clickCropperPosRel.y = this.pos.rel.top;
            next.y = cropperImage.pos.rel.top + cropperImage.height - this.height;
        }

        this.el.style.left = next.x + 'px';
        this.el.style.top = next.y + 'px';
        this.updateTransform();
    }

    public transformCropper(handle: Handle, cropperImage: CropperImage, cursor: PointerEvent) {
        let next = {
            w: 0,
            h: 0
        }

        switch (handle.type) {
            case HandleType.TL:
                next = {
                    w: this.width - (cursor.clientX - this.clickCursorPosAbs.x),
                    h: this.height - (cursor.clientY - this.clickCursorPosAbs.y)
                }
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
                } else if (next.w + this.pos.rel.right >= cropperImage.width + cropperImage.pos.rel.right)
                    next.w = cropperImage.width - (this.pos.rel.right - cropperImage.pos.rel.right);
                this.el.style.width = next.w + "px";
                this.el.style.height = Number(window.getComputedStyle(this.el).width.slice(0, -2)) * 1 / this.ar + "px";
                break;
            case HandleType.TR:
                next = {
                    w: this.width + cursor.clientX - this.clickCursorPosAbs.x,
                    h: this.height - (cursor.clientY - this.clickCursorPosAbs.y)
                }
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
                } else if (next.w + this.pos.rel.left >= cropperImage.width + cropperImage.pos.rel.left)
                    next.w = cropperImage.width - (this.pos.rel.left - cropperImage.pos.rel.left);

                this.el.style.width = next.w + "px";
                this.el.style.height = Number(window.getComputedStyle(this.el).width.slice(0, -2)) * 1 / this.ar + "px";
                break;

            case HandleType.BL:
                next = {
                    w: this.width - (cursor.clientX - this.clickCursorPosAbs.x),
                    h: this.height + cursor.clientY - this.clickCursorPosAbs.y
                }
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
                } else if (next.w + this.pos.rel.right >= cropperImage.width + cropperImage.pos.rel.right)
                    next.w = cropperImage.width - (this.pos.rel.right - cropperImage.pos.rel.right);
                this.el.style.width = next.w + "px";
                this.el.style.height = Number(window.getComputedStyle(this.el).width.slice(0, -2)) * 1 / this.ar + "px";
                break;
            case HandleType.BR:
                next = {
                    w: this.width + cursor.clientX - this.clickCursorPosAbs.x,
                    h: this.height + cursor.clientY - this.clickCursorPosAbs.y
                }
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
                } else if (next.w + this.pos.rel.left >= cropperImage.width + cropperImage.pos.rel.left)
                    next.w = cropperImage.width - (this.pos.rel.left - cropperImage.pos.rel.left);
                this.el.style.width = next.w + "px";
                this.el.style.height = Number(window.getComputedStyle(this.el).width.slice(0, -2)) * 1 / this.ar + "px";
                break;
        }
        this.clickCursorPosAbs = {
            x: cursor.clientX,
            y: cursor.clientY
        }
        this.updateTransform();

    }
}

enum HandleType { TL = "TL", TR = "TR", BL = "BL", BR = "BR" }

type Handle = {
    el: HTMLSpanElement, clicked: boolean, type: HandleType,
}

interface ICropperHandles {
    handles: Handle[];
}

class CropperHandles implements ICropperHandles {
    handles: Handle[];

    constructor(cropperId: string, cropper: Cropper) {
        const tl: Handle = { el: document.querySelector("#" + cropperId + " .crop__crophandle--tl")! as HTMLSpanElement, clicked: false, type: HandleType.TL };
        const tr: Handle = { el: document.querySelector("#" + cropperId + " .crop__crophandle--tr")! as HTMLSpanElement, clicked: false, type: HandleType.TR };
        const br: Handle = { el: document.querySelector("#" + cropperId + " .crop__crophandle--br")! as HTMLSpanElement, clicked: false, type: HandleType.BR };
        const bl: Handle = { el: document.querySelector("#" + cropperId + " .crop__crophandle--bl")! as HTMLSpanElement, clicked: false, type: HandleType.BL };

        this.handles = Array<Handle>(tl, tr, br, bl);
        for (const handle of this.handles) {
            handle.el.addEventListener("pointerdown", (cursor) => {
                cursor.preventDefault();
                handle.clicked = true;
                cropper.clickCursorPosAbs = {
                    x: cursor.clientX,
                    y: cursor.clientY
                }
                cropper.updateTransform();
                cropper.clickCropperPosRel = {
                    x: cropper.pos.rel.left,
                    y: cropper.pos.rel.top
                }
                cursor.stopPropagation();
            })
        }
        window.addEventListener("pointerup", () => {
            for (const handle of this.handles) {
                handle.clicked = false;
            }
        })
    }
}

class Crop {
    private cropper: Cropper;
    private cropperBox: CropperBox;
    private cropperImage: CropperImage;
    private cropperHandles = {} as CropperHandles;
    private form: HTMLFormElement;

    constructor(crop: HTMLDivElement, ar: number) {

        const input = document.querySelector("#" + crop.id + " .crop__input")! as HTMLInputElement;
        const submit = document.querySelector("#" + crop.id + " .crop__submit")! as HTMLButtonElement;
        this.form = document.querySelector("#" + crop.id + " .crop__form")! as HTMLFormElement;
        submit.disabled = true;
        submit.addEventListener("click", () => {
            const cropped = this.submitCropped();
            document.body.append(cropped);
        });
        this.cropper = new Cropper(document.querySelector("#" + crop.id + " .crop__cropper")!);
        this.cropper.el.style.visibility = "hidden";
        document.querySelector("#" + crop.id + " .crop__cropper-container")!
        input.addEventListener("change", () => {
            if (input.value != "") {
                this.cropperBox = new CropperBox(document.querySelector("#" + crop.id + " .crop__cropper-container")!);
                crop.dataset.src! = URL.createObjectURL(input.files![0]);
                (document.querySelector("#" + crop.id + " .crop__cropper-image")! as HTMLInputElement).dataset.src = URL.createObjectURL(input.files![0]);
                this.cropperImage = new CropperImage(
                    document.querySelector("#" + crop.id + " .crop__cropper-image")!,
                    document.querySelector("#" + crop.id + " .crop__cropper-image-clipped")!,
                    this.cropperBox,
                    () => {
                        submit.disabled = false;
                        this.setAspectRatio(ar);
                        this.cropper.el.style.visibility = "visible";
                        this.cropper.resize(this.cropperImage);
                        this.cropperHandles = new CropperHandles(crop.id, this.cropper);
                        this.initUpdate();
                    }
                );


            }
        })

    }

    private initUpdate() {
        document.addEventListener("pointermove", (cursor) => {
            if (this.cropper.clicked) {
                this.cropper.move(cursor, this.cropperImage);
                this.cropperImage.updateImageClip(this.cropper);
            }
            for (const handle of this.cropperHandles.handles) {
                if (handle.clicked) {
                    this.cropper.transformCropper(handle, this.cropperImage, cursor);
                    this.cropperImage.updateImageClip(this.cropper);
                }
            }
        });
        window.addEventListener("resize", () => {
            this.cropperBox.updateAspectRatio();
            this.cropperImage.resize(this.cropperBox);
            this.cropper.resize(this.cropperImage);
        });
    }

    public setAspectRatio(ar: number) {
        this.cropper.ar = ar;
        if (this.cropperImage.loaded)
            this.cropper.resize(this.cropperImage);
    }

    public submitCropped(): HTMLCanvasElement {
        // create a canvas that will present the output image
        const imageOut = document.createElement("canvas");
        // set it to the same size as the image
        imageOut.width = this.cropperImage.imageWidth * this.cropper.width / this.cropperImage.width;
        imageOut.height = this.cropperImage.imageHeight * this.cropper.height / this.cropperImage.height;

        const ctx = imageOut.getContext("2d")!;
        ctx.drawImage(
            this.cropperImage.el,
            -(this.cropper.pos.rel.left - this.cropperImage.pos.rel.left) * this.cropperImage.imageWidth / this.cropperImage.el.width,
            -(this.cropper.pos.rel.top - this.cropperImage.pos.rel.top) * this.cropperImage.imageHeight / this.cropperImage.el.height
        );

        imageOut.toBlob((blob: Blob)=> {
            const formDataImage = document.createElement("input");
            formDataImage.name = "file";
            formDataImage.type = "file";
            
            const container = new DataTransfer();
            container.items.add(new File([blob], "img.jpeg", {type:"image/jpeg", lastModified:new Date().getTime()}));
            console.log()
            formDataImage.files = container.files;
            this.form.append(formDataImage)
            this.form.submit();
        }, 'image/jpeg', 1.0)

        return imageOut;
    }

}
