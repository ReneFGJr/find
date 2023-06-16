import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { IsbnApiService } from '../../../service/Api/isbn-api.service';

@Component({
  selector: 'app-books-insert-isbn',
  templateUrl: './books-insert-isbn.component.html',
  styleUrls: ['./books-insert-isbn.component.scss']
})
export class BooksInsertIsbnComponent {

  public dataIsbn: Array<string> = [];

  ngOnInit() { }

  public statusForm = '';
  public isbnAddForm: FormGroup = this.fb.group(
    {
      isbn: ['',[Validators.required, Validators.minLength(10), Validators.maxLength(13)]],
      nrpat: [''],
      autoNumber: ['1']
    }
  )

  public submitForm()
    {
      //9788585637262
      if (this.isbnAddForm.valid)
      {
        console.log(this.isbnAddForm.value.isbn)
        this.statusForm = 'Enviado';
        let isbn = this.isbnAddForm.get('isbn')?.value;

        /************* Valida ISBN */
        this.IsbnApiService.validISBN(isbn.trim()).subscribe(IsbnApiService => {
          console.log(IsbnApiService.valid);
          if (IsbnApiService.valid)
            {
              this.statusForm = '';
            } else {
              this.statusForm = 'Número do ISBN Inválido';
            }


        },
        (error)=>{
          console.log(error);
        }
        );

      } else {

      }

    }

  constructor(private fb: FormBuilder, private IsbnApiService:IsbnApiService) {

  }
}
