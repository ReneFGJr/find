import { Component } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-catalog-status',
  standalone: false,
  templateUrl: './catalog-status.component.html',
  styleUrl: './catalog-status.component.scss',
})
export class CatalogStatusComponent {
  status: Array<any> = [];
  constructor(private findService: FindService) {}

  ngOnInit() {
    let dt = {};
    this.findService.api_post('catalog/status', dt).subscribe((res) => {
      this.status = res;
    });
  }
}
