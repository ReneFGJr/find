import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-catalog-show',
  standalone: false,
  templateUrl: './catalog-show.component.html',
  styleUrl: './catalog-show.component.scss',
})
export class CatalogShowComponent {
  @Input() books: any = {};
  isLoading: boolean = false;
  selectedBook: any = null;
  public isbn: string = '9788585445980'; //9788578110012
  public libraryID: string = '1016';

  openBookDetails(book: any) {
    this.isbn = book.isbn;
    this.selectedBook = book;
  }

  closeBookDetails() {
    this.selectedBook = null;
  }
}
