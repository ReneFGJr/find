import { FindService } from 'src/app/000_core/service/find.service';
import { Component, Input } from '@angular/core';
import { Observable } from 'rxjs';
import { UploadService } from '../../service/upload.service';
import { HttpEventType, HttpResponse } from '@angular/common/http';
import { Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'app-upload-image',
  templateUrl: './upload-image.component.html',
  styleUrls: ['./upload-image.component.scss']
})
export class UploadImageComponent {
  @Input() public isbn:string = '';
  @Output() newItemEvent = new EventEmitter<string>();

  /***************************/
  selectedFiles?: FileList;
  currentFile?: File;
  progress = 0;
  message = '';
  preview = '';
  temp:Array<any> | any

  imageInfos?: Observable<any>;

  constructor(
    private uploadService: UploadService,
    private findService: FindService) { }

  ngOnInit(): void {

  }

  upload(): void {
    this.progress = 0;

    if (this.selectedFiles) {
      const file: File | null = this.selectedFiles.item(0);

      if (file) {
        this.currentFile = file;
        this.uploadService.upload(this.currentFile).subscribe({
          next: (event: any) => {
            if (event.type === HttpEventType.UploadProgress) {
              this.progress = Math.round((100 * event.loaded) / event.total);
            } else if (event instanceof HttpResponse) {
              this.message = event.body.message;
            }

          },
          error: (err: any) => {
            console.log(err);
            this.progress = 0;

            if (err.error && err.error.message) {
              this.message = err.error.message;
            } else {
              this.message = 'Could not upload the image!';
            }
            this.currentFile = undefined;
          },
        });
      }
      this.selectedFiles = undefined;
    }
  }

  selectFile(event: any): void {
    this.message = '';
    this.preview = '';
    this.progress = 0;
    this.selectedFiles = event.target.files;

    if (this.selectedFiles) {
      const file: File | null = this.selectedFiles.item(0);

      if (file) {
        this.preview = '';
        this.currentFile = file;

        const reader = new FileReader();

        reader.onload = (e: any) => {
          console.log("Realizando Carga")
          //this.preview = e.target.result;
          this.findService.saveCover(this.isbn,e.target.result).subscribe(
            res=>{
              this.message = 'Arquivo salvo';
              this.temp = res
              console.log('===============')
              console.log(this.temp.cover);
              this.newItemEvent.emit(this.temp.cover);
            }
          )
        };
        reader.readAsDataURL(this.currentFile);
      }
    }
  }
}
