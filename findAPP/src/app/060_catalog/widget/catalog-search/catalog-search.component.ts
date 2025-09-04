import { Component } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-catalog-search',
  standalone: false,
  templateUrl: './catalog-search.component.html',
  styleUrl: './catalog-search.component.scss',
})
export class CatalogSearchComponent {
  data: Array<any> | any

  constructor(
    private findService: FindService
  ) {}

  searchQuery(event: Event) {
    console.log("+++++++++++++++++++++++++++++++");
    let dt = {q: event};
    console.log(dt);
    this.findService.api_post('catalog/search', dt).subscribe((res) => {
      this.data = res;
    });
  }
}
