import { Component, Input } from '@angular/core';
import { FormBuilder, FormGroup, FormControl } from '@angular/forms';
import { Router } from '@angular/router';
import { FindService } from 'src/app/000_core/service/find.service';
import { Book } from "../../../units/books";

@Component({
  selector: 'app-preview',
  templateUrl: './preview.component.html',
  styleUrls: ['./preview.component.scss']
})
export class PreviewComponent {
  formBook?: FormGroup;

  constructor(
    private findService: FindService,
    private router: Router,
    private fb: FormBuilder
  ) { }


  edit_title:boolean = false;
  bk_title: string = '';
  isbn: string = '';
  @Input() public book:Array<any>|any

  ngOnInit()
    {
      this.createForm(new Book());
    }

  createForm(book: Book) {
    this.formBook = this.fb.group({
      title: new FormControl(book.title),
      isbn: new FormControl(book.isbn),

    })
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
      alert(this.bk_title);
      this.findService.saveData(this.book.isbn, 'bk_title', this.bk_title).subscribe(
        res=>
          {
            this.book = res;
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
