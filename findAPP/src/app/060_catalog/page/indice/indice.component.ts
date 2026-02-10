import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { FindService } from '../../../010_core/service/find.service';

import { map, filter, tap, switchMap } from 'rxjs/operators';



@Component({
  selector: 'app-indice-catalog',
  standalone: false,
  templateUrl: './indice.component.html',
  styleUrl: './indice.component.scss'
})
export class IndiceComponent implements OnInit {

  type: string = '';
  data: Array<any> | any

  constructor(
    private route: ActivatedRoute,
    private findService: FindService
  ) {}

  ngOnInit(): void {
    this.route.paramMap
      .pipe(
        map(params => params.get('type')),
        filter((type): type is string => type !== null),
        tap(type => this.type = type),
        switchMap(type =>
          this.findService.api_post(`indices/${type}`)
        )
      )
      .subscribe({
        next: (res) => {
          this.data = res
          console.log('Índice carregado:', this.data);
        },
        error: (err) => {
          console.error('Erro ao carregar índice:', err);
        }
      });
  }
}
