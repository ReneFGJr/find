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
  public result: Array<any> | any;
  public isLoading: boolean = true;

  constructor(
    private findService: FindService, // private router: Router
    private localStorage: LocalStorageService, // private localStorage: LocalStorageService
    private routes: Router
  ) {
    console.log('constructor app component');
  }

  goBook(id: string, library: string) {}

  searchTerm(search: Event) {
    console.log('searchTerm', search);
    this.isLoading = true;
    this.books = null;

    console.log('searchTerm', search);

    let dt = { q: search };

    this.findService.api_post('search/' + this.libraryID, dt).subscribe(
      (res) => {
        this.books = res;
        this.isLoading = false;
        console.log(res)
      }
    );
  }

  openBookDetails(book: any) {
    this.isbn = book.isbn;
    this.selectedBook = book;
  }

  closeBookDetails() {
    this.selectedBook = null;
  }

  ngOnInit() {
    console.log('--Init Vitrine--');
    this.libraryID = localStorage.getItem('library') || '';
    if (this.libraryID == '') {
      console.log('--MSG: Library empty--');
      this.routes.navigate(['/selectLibrary']);
    } else {
      this.findService
        .api_post('vitrine/' + this.libraryID, [])
        .subscribe((res) => {
          this.books = res;
          console.log(this.books);
          this.libraries = this.books;

          if (!this.libraries) {
            this.localStorage.remove('library');
            this.libraryID = '';
          }
          this.isLoading = false;
        });
    }
  }
}
