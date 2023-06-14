import { Component } from '@angular/core';
import { LibraryApiService } from '../../service/Api/library-api.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-library-admin',
  templateUrl: './library-admin.component.html',
  styleUrls: ['./library-admin.component.scss']
})
export class LibraryAdminComponent {

  LibraryData: Array<any> = new Array();
  LibrarySize: number = 0;

  constructor(private fb: FormBuilder, private LibraryApiService:LibraryApiService) {
  }

  ngOnInit()
    {
      this.LibraryApiService.getLibraries().subscribe(LibraryData => {
          this.LibraryData = LibraryData;
          this.LibrarySize = LibraryData.length;
          console.log(LibraryData.length);
          console.log(LibraryData);
        },
        (error)=>{
          console.log(error);
        }
      );
    }
}
