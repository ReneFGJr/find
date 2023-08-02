import { FindService } from './../../../000_core/service/find.service';
import { Component } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-isbn-add',
  templateUrl: './isbn-add.component.html',
  styleUrls: ['./isbn-add.component.scss']
})
export class IsbnAddComponent {

  constructor(private findService:FindService) {}

  name = new FormControl('');
  message: string = '';
  isbn: string = '';
  book: Array<any> | any
  valid: boolean = false;

  isbnForm = new FormGroup({
    isbn: new FormControl('', Validators.required),
  });

  limpar_isbn()
    {
    this.isbnForm = new FormGroup({
      isbn: new FormControl('', Validators.required),
    });
    this.book = null;
    this.isbn = '';
    this.valid = false;
    }

  submitISBN()
    {

      if (this.isbnForm.valid)
        {
          this.message = '';
          let isbn = this.isbnForm.value.isbn!

          console.log(isbn);
          this.findService.validISBN(isbn).subscribe(
          res=>
            {
              console.log(res)
              this.book = res;
              if (this.book.valid)
                {
                  this.valid = true;
                  this.findService.addISBN(isbn).subscribe(
                    res=>{
                      this.book = res
                      console.log(res)
                      this.isbn = this.book.isbn13
                      this.isbnForm.value.isbn = ''
                    }
                  )
                } else {
                  this.valid = false;
                  this.message = 'Número do ISBN é inválido';
                }
            }
        )
        } else {
          this.message = 'Número do ISBN é obrigatório';
        }
    }
}
