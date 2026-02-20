import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { FindService } from '../../010_core/service/find.service';

import { map, filter, tap, switchMap } from 'rxjs/operators';

@Component({
  selector: 'app-index-v',
  standalone: false,
  templateUrl: './index-v.component.html',
  styleUrl: './index-v.component.scss',
})
export class IndexVComponent {
  id: string = '';
  data: Array<any> | any;
  ISBN: string = '';
  libraryID: string = localStorage.getItem('library') || '';

  constructor(
    private route: ActivatedRoute,
    private findService: FindService,
  ) {}

  ngOnInit(): void {
    this.route.paramMap
      .pipe(
        map((params) => params.get('id')),
        filter((id): id is string => id !== null),
        tap((id) => (this.id = id)),
        switchMap((id) => this.findService.api_post(`v/${id}`)),
      )
      .subscribe({
        next: (res) => {
          this.data = res;
          console.log('Carregado:', this.data);
          for (const key in this.data.data) {
            if (this.data.data[key]?.Property === 'isAppellationOfExpression') {
              this.ISBN = this.data.data[key].Caption?.substring(5, 18) || '';
              break;
            }
          }
        },
        error: (err) => {
          console.error('Erro ao carregar registro:', err);
        },
      });
  }
}
