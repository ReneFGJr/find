import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-item-places',
  templateUrl: './item-places.component.html',
})
export class ItemPlacesComponent {
  @Input() public book:Array<any> | any = []
}
