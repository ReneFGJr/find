import { Component, Input } from '@angular/core';
import { FormBuilder, FormGroup, FormControl, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { FindService } from 'src/app/000_core/service/find.service';
import { uBook } from "../../../units/books";

@Component({
  selector: 'app-preview',
  templateUrl: './preview.component.html',
  styleUrls: ['./preview.component.scss']
})
export class PreviewComponent {
  @Input() public book:Array<any>|any

  constructor(
    private findService: FindService,
    private router: Router,
    private formBuilder: FormBuilder
  ) {}

  formBook = new FormGroup({
    title: new FormControl('', Validators.required),
    isbn: new FormControl(''),
  });

  edit_title:boolean = false;
  title: string = '';
  isbn: string = '';

  /************************* */
  ngOnInit()
    {
        this.formBook.setValue({title:this.book.bk_title,isbn:'0123'});
    }

  edit(field:string)
    {
      if (field=='title')
        {
          this.edit_title = true;
        }
    }

  save(field: string) {
    if (field == 'title') {
      this.edit_title = false;
      let value = this.formBook.value.title as string;
      this.findService.saveData(this.book.isbn, field, value).subscribe(
        res=>
          {
            console.log("OK")
            //this.book = res;
          }
      )
    }
  }

  cancel(field: string) {
    if (field == 'title') {
      this.edit_title = false;
    }
  }

}
