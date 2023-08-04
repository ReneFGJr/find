import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-item-add-isbn',
  templateUrl: './item-add-isbn.component.html',
  styleUrls: ['./item-add-isbn.component.scss']
})
export class ItemAddIsbnComponent {
  @Input() public isbn: string = '';
  @Input() public book: Array<any>|any;
  tombo: string = '';
  ngOnInit()
    {
      this.tombo = this.book.be_isbn13;
    }
}
