import { FindService } from './../../../010_core/service/find.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-report-item',
  standalone: false,
  templateUrl: './report-item.component.html',
  styleUrl: './report-item.component.scss',
})
export class ReportItemComponent {
  constructor(private FindService: FindService) {}
  data: any = {};
  ngOnInit() {
    this.FindService.api_post('/report/item', {}).subscribe((response) => {
      this.data = response;
    });
  }

  filtro = '';

  get itensFiltrados() {
    const items = this.data?.items || [];
    const q = (this.filtro || '').toLowerCase().trim();
    if (!q) return items;

    return items.filter((it: any) => {
      const hay =
        `${it.i_tombo ?? ''} ${it.i_titulo ?? ''} ${it.i_identifier ?? ''} ` +
        `${it.i_ln1 ?? ''} ${it.i_ln2 ?? ''} ${it.i_ln3 ?? ''}`.toLowerCase();
      return hay.includes(q);
    });
  }

  trackByTombo = (_: number, it: any) => it?.i_tombo ?? it;
}
