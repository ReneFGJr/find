import { Component } from '@angular/core';
import { ActivatedRoute, Params, Router } from '@angular/router';

@Component({
  selector: 'app-book',
  templateUrl: './book.component.html',
  styleUrls: ['./book.component.scss']
})
export class BookComponent {

  public id:string = ''

  constructor(private ActivatedRoute: ActivatedRoute) { }

  ngOnInit() {
    this.ActivatedRoute.params.subscribe(
      res => console.log(res)
    )
    /*********** GET *********/
    this.ActivatedRoute.queryParams.subscribe(
      res => console.log(res)
    )
  }
}
