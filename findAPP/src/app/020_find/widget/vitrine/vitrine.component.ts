import { Component } from '@angular/core';
import { FindService } from '../../../000_core/service/find.service';
import { LocalStorageService } from '../../../000_core/service/local-storage.service';
import { routes } from '../../../app.routes';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-vitrine',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './vitrine.component.html',
  styleUrl: './vitrine.component.scss',
})
export class VitrineComponent {
  public books: Array<any> | any;
  public libraries: any[] = [];
  public libraryID: string = '';
  public library: any[] = [];
  public selectedBook: any = null;

  constructor(
    private findService: FindService, // private router: Router
    private localStorage: LocalStorageService // private localStorage: LocalStorageService
  ) {
    console.log('constructor app component');
  }

  goBook(id: string, library: string) {}

  openBookDetails(book: any) {
    this.selectedBook = book;
  }

  closeBookDetails() {
    this.selectedBook = null;
  }

  ngOnInit() {
    console.log('ngOnInit app component');
    this.libraryID = localStorage.getItem('library') || '';
    if (this.libraryID == '') {
      [routes[0].path] = ['selectLibrary'];
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
