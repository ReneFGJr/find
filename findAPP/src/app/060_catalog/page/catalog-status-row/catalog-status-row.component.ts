import { Component, inject } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { filter, map, switchMap, tap } from 'rxjs';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-catalog-status-row',
  standalone: false,
  templateUrl: './catalog-status-row.component.html',
  styleUrls: ['./catalog-status-row.component.scss'],
})
export class CatalogStatusRowComponent {
  private route = inject(ActivatedRoute);
  private findService = inject(FindService);

  id = 0;
  data: any;

  private router = inject(Router);

  placeholderCover = 'assets/img/cover-placeholder.png'; // troque pelo caminho que preferir

  onImgError(ev: Event) {
    const img = ev.target as HTMLImageElement;
    if (img && img.src !== this.placeholderCover) {
      img.src = this.placeholderCover;
    }
  }

  trackById = (_: number, item: any) =>
    item?.i_identifier ?? item?.id ?? `${item?.i_tombo}`;

  onView(item: any) {
    const id = item?.id_i;
    // ajuste a rota conforme seu app:
    this.router.navigate(['/catalog/item', id]);
  }

  /** Normaliza o status para uma classe semântica */
  private normStatus(
    raw: any
  ): 'ok' | 'info' | 'pending' | 'warning' | 'error' | 'muted' {
    const s = String(raw || '')
      .trim()
      .toLowerCase();

    // mapeie aqui os seus statuses reais
    if (
      [
        'catalogado',
        'concluído',
        'concluido',
        'ativo',
        'disponível',
        'disponivel',
        'ok',
      ].includes(s)
    )
      return 'ok';
    if (
      [
        'em processamento',
        'processando',
        'em análise',
        'em analise',
        'andamento',
      ].includes(s)
    )
      return 'pending';
    if (['aguardando', 'pendente', 'fila', 'espera'].includes(s))
      return 'warning';
    if (['erro', 'falha', 'inconsistente', 'rejeitado'].includes(s))
      return 'error';
    if (['informativo', 'revisado', 'atualizado'].includes(s)) return 'info';
    return 'muted';
  }

  statusBadgeClass(status: any) {
    return this.normStatus(status);
  }
  statusBtnClass(status: any) {
    return this.normStatus(status);
  }
  statusRowClass(status: any) {
    return this.normStatus(status);
  }

  ngOnInit() {
    this.route.paramMap
      .pipe(
        map((params) => Number(params.get('id'))),
        filter((id) => !Number.isNaN(id)), // garante ID válido
        tap((id) => {
          this.id = id;
        }),
        switchMap((id) => {
          let dt = { status: this.id };
          return this.findService.api_post('catalog/statusID', dt);
        })
      )
      .subscribe((res) => {
        this.data = res;
      });
  }
}
