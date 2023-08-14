import { FindService } from './../../../000_core/service/find.service';
import { Component } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';

@Component({
  selector: 'app-isbn-add',
  templateUrl: './isbn-add.component.html',
  styleUrls: ['./isbn-add.component.scss']
})
export class IsbnAddComponent {

  constructor(
      private findService:FindService,
      private router: Router
      ) {}

  name = new FormControl('');
  message: string = '';
  isbn: string = '';
  book: Array<any> | any
  vBook: Array<any> | any
  valid: boolean = false;

  isbnForm = new FormGroup({
    isbn: new FormControl('', Validators.required),
  });

  limpar_isbn()
    {
    this.isbnForm.setValue({isbn:''});
    this.book = [];
    this.vBook = [];
    }

  ngOnInit()
    {
      this.isbnForm.setValue({isbn:'9788571933422'});
    }

  submitISBN()
    {
      if (this.isbnForm.valid)
        {
          this.message = '';
          this.isbn = this.isbnForm.value.isbn!

          this.findService.validISBN(this.isbn).subscribe(
          res=>
            {
              console.log(res)
              this.book = res;
              if (this.book.valid)
                {
                  this.valid = true;
                  this.findService.addISBN(this.isbn).subscribe(
                    res=>{
                      this.book = res
                      this.vBook = res;
                      console.log(res)
                      this.isbn = this.book.isbn13
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
