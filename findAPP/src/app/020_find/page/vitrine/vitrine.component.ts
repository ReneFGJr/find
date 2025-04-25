import { Component } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { LocalStorageService } from '../../../010_core/service/local-storage.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-vitrine',
  standalone: false,
  templateUrl: './vitrine.component.html',
  styleUrl: './vitrine.component.scss',
})
export class VitrineComponent {
  public books: Array<any> | any;
  public libraries: any[] = [];
  public libraryID: string = '';
  public library: any[] = [];
  public selectedBook: any = null;
  public isbn: string = '9788585445980'; //9788578110012

  constructor(
    private findService: FindService, // private router: Router
    private localStorage: LocalStorageService, // private localStorage: LocalStorageService
    private routes: Router
  ) {
    console.log('constructor app component');
  }

  goBook(id: string, library: string) {}

  openBookDetails(book: any) {
    this.isbn = book.isbn;
    this.selectedBook = book;
  }

  closeBookDetails() {
    this.selectedBook = null;
  }

  ngOnInit() {
    console.log('ngOnInit app component');
    this.libraryID = localStorage.getItem('library') || '';
    if (this.libraryID == '') {
      this.routes.navigate(['/selectLibrary']);
    } else {
      this.findService
        .api_post('vitrine/' + this.libraryID, [])
        .subscribe((res) => {
          this.books = res;
          this.libraries = this.books;

          if (!this.libraries) {
            this.localStorage.remove('library');
            this.libraryID = '';
          }
          console.log(this.books);
        });
    }
  }
}
