import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-catalog-item-show',
  standalone: false,
  templateUrl: './catalog-item-show.component.html',
  styleUrl: './catalog-item-show.component.scss',
})
export class CatalogItemShowComponent {
  @Input() data: any;
  @Input() editMode: boolean = false;
  @Output() response = new EventEmitter<string>();

  onAdd(id:string) {
    this.response.emit(id);
  }
}
