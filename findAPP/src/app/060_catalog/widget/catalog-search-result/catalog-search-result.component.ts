import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-catalog-search-result',
  standalone: false,
  templateUrl: './catalog-search-result.component.html',
  styleUrl: './catalog-search-result.component.scss'
})
export class CatalogSearchResultComponent {
    @Input() data: Array<any> | any;

}
