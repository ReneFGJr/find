import { FindService } from 'src/app/000_core/service/find.service';
import { Component } from '@angular/core';
import { ActivatedRoute, Params, Router } from '@angular/router';

@Component({
  selector: 'app-book',
  templateUrl: './book.component.html',
  styleUrls: ['./book.component.scss'],
})
export class BookComponent {
  public id: string = '';
  public book: Array<any> | any;
  public lib: string = '';

  constructor(
    private ActivatedRoute: ActivatedRoute,
    private findService: FindService
  ) {}

  ngOnInit() {
    this.ActivatedRoute.params.subscribe((res) => {
      this.book = res;
      this.id = this.book.id;
      this.lib = this.book.lib;

      /*********** GET *********/
      this.ActivatedRoute.queryParams.subscribe((res) => {
        let dt: Array<any> | any = { isbn: this.id, lib: this.lib };
        this.findService.api_post('getIsbn', dt).subscribe((res) => {
          console.log(res);
          this.book = res;
        });
      });
    });
  }
}
