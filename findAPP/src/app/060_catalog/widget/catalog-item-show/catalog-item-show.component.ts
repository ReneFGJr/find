import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-catalog-item-show',
  standalone: false,
  templateUrl: './catalog-item-show.component.html',
  styleUrl: './catalog-item-show.component.scss'
})
export class CatalogItemShowComponent {
  @Input() data: any;
}
