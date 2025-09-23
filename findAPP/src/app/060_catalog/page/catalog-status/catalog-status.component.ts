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

  ngOnInit() {}
}
