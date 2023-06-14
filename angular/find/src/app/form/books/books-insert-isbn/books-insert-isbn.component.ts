import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-books-insert-isbn',
  templateUrl: './books-insert-isbn.component.html',
  styleUrls: ['./books-insert-isbn.component.scss']
})
export class BooksInsertIsbnComponent {
  public statusForm = '';
  public isbnAddForm: FormGroup = this.fb.group(
    {
      isbn: ['',[Validators.required, Validators.minLength(10), Validators.maxLength(13)]]
    }
  )

  public submitForm()
    {
      if (this.isbnAddForm.valid)
      {
        console.log(this.isbnAddForm.value.isbn)
        this.statusForm = 'Enviado';
      } else {

      }

    }

  constructor(private fb: FormBuilder) {

  }
}
