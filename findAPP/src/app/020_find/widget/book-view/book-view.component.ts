import { Component, Input } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { LocalStorageService } from '../../../010_core/service/local-storage.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-view-book',
  standalone: false,
  templateUrl: './book-view.component.html',
  styleUrl: './book-view.component.scss',
})
export class BookViewComponent {
  @Input() public isbn: string = '9788585445980'; //9788578110012
  @Input() public libraryID: string = '1003'; //1003

  //https://www.ufrgs.br/find/v2/api/getIsbn?isbn=9788585445980&lib=1003
  public book: Array<any> | any;

  constructor(
    private findService: FindService, // private router: Router
    private localStorage: LocalStorageService, // private localStorage: LocalStorageService
    private route: Router
  ) {
    console.log('constructor app component');
  }

  goBook(id: string, library: string) {}

  // Método auxiliar para concatenar os nomes das editoras
  getPublisher(): string {
    return 'X';
    //return this.book.meta.Publisher.map((pub) => pub.name).join(', ');
  }

  ngOnChanges() {
    console.log('ngChage', this.isbn);
    this.libraryID = localStorage.getItem('library') || '';
    if (this.libraryID == '') {
      this.route.navigate(['/selectLibrary']);
    } else {
      let dt: Array<any> = [{ isbn: this.isbn, lib: this.libraryID }];
      console.log('ngChage', dt);
      this.findService
        //https://www.ufrgs.br/find/v2/api/getIsbn?isbn=9788585445980&lib=1003
        .api_post('getIsbn', dt[0])
        .subscribe((res) => {
          this.book = res;
          console.log(this.book);
        });
    }
  }
}
