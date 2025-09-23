import { Component } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { ActivatedRoute } from '@angular/router';
import { map } from 'rxjs';

@Component({
  selector: 'app-catalog-status',
  standalone: false,
  templateUrl: './catalog-status.component.html',
  styleUrl: './catalog-status.component.scss',
})
export class CatalogStatusComponent {
  data: Array<any> | any;
  id: number = 0;

  constructor(
    private findService: FindService,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    console.log('CatalogStatusComponent ngOnInit - 2');
    this.route.paramMap
      .pipe(map((p) => Number(p.get('id'))))
      .subscribe((id) => (this.id = id));
    console.log('ID =', this.id);

    this.findService
      .api_post('catalog/statusID/' + this.id)
      .pipe(takeUntilDestroyed())
      .subscribe({
        next: (res) => {
          this.data = res;
        },
      });
  }

  takeUntilDestroyed() {
    {
      console.log('Hello 2');
    }
  }
}
