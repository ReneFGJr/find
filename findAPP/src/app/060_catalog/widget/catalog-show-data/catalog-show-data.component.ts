import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Offcanvas } from 'bootstrap';

@Component({
  selector: 'app-catalog-show-data',
  standalone: false,
  templateUrl: './catalog-show-data.component.html',
  styleUrl: './catalog-show-data.component.scss',
})
export class CatalogShowDataComponent {
  @Input() data: any;
  @Input() range: string = '';
  @Output() action = new EventEmitter<string>();

  offcanvasInstance: any;

  editItem() {
    this.action.emit(this.data);

  }

  deleteItem(id: string) {
    // Placeholder for delete functionality
  }
}
