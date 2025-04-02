import { Component, Input } from '@angular/core';
import { FindService } from '../../../000_core/service/find.service';
import { LocalStorageService } from '../../../000_core/service/local-storage.service';
import { routes } from '../../../app.routes';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-view-book',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './book.component.html',
  styleUrl: './book.component.scss',
})
export class BookComponent {
  @Input() public isbn: string = '9788585445980'; //9788578110012
  @Input() public libraryID: string = '1003'; //1003

  //https://www.ufrgs.br/find/v2/api/getIsbn?isbn=9788585445980&lib=1003
  public book: Array<any> | any;

  constructor(
    private findService: FindService, // private router: Router
    private localStorage: LocalStorageService // private localStorage: LocalStorageService
  ) {
    console.log('constructor app component');
  }

  goBook(id: string, library: string) {}

  // Método auxiliar para concatenar os nomes das editoras
  getPublisher(): string {
    return "X"
    //return this.book.meta.Publisher.map((pub) => pub.name).join(', ');
  }

  ngOnChanges() {
    console.log('ngChage', this.isbn);
    this.libraryID = localStorage.getItem('library') || '';
    if (this.libraryID == '') {
      [routes[0].path] = ['selectLibrary'];
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
