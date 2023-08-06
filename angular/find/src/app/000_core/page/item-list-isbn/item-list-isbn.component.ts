import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-item-list-isbn',
  templateUrl: './item-list-isbn.component.html',
  styleUrls: ['./item-list-isbn.component.scss']
})
export class ItemListIsbnComponent {
  @Input() public isbn:string = '';
  @Input() public book: Array<any>|any
}
