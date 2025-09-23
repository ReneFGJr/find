import { Component } from '@angular/core';
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

  id!: number;

  constructor(private route: ActivatedRoute) {}

  ngOnInit() {
    console.log('CatalogStatusComponent ngOnInit');
    this.route.paramMap
      .pipe(
        map((p) => Number(p.get('id'))),
        takeUntilDestroyed()
      )
      .subscribe((id) => (this.id = id));
  }
}
