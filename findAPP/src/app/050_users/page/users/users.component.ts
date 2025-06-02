import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-users',
  standalone: false,
  templateUrl: './users.component.html',
  styleUrl: './users.component.scss',
})
export class UsersComponent implements OnInit, OnDestroy {
  public act: string = 'list';
  public userID = 0;
  public loading: boolean = false;

  private routeSub!: Subscription;

  constructor(private route: ActivatedRoute) {}

  ngOnInit() {
    // Assina o paramMap para reagir sempre que a rota (':act' ou ':id') mudar
    this.routeSub = this.route.paramMap.subscribe((params: ParamMap) => {
      this.act = params.get('act') ?? 'list';
      this.userID = Number(params.get('id')) || 0;

      // Exemplo: dispara lógica de carregamento de acordo com act
      this.loading = true;
      // Aqui você poderia chamar, por exemplo:
      // if (this.act === 'view' && this.userID > 0) { this.loadUser(this.userID); }
      // ou this.loadList() se act === 'list'
      // Ao fim do carregamento, defina this.loading = false.
      //
      // Para este exemplo genérico, apenas simulamos que o carregamento foi concluído:
      setTimeout(() => {
        this.loading = false;
      });
    });
  }

  ngOnDestroy() {
    // Cancela a assinatura para evitar vazamento de memória
    if (this.routeSub) {
      this.routeSub.unsubscribe();
    }
  }

  // Exemplo de método que carregaria a lista de usuários
  loadList(): void {
    // this.loading = true;
    // this.findService.api_get('users/list').subscribe({ ... this.loading = false });
  }

  // Exemplo de método que carregaria os detalhes de um usuário
  loadUser(userId: number): void {
    // this.loading = true;
    // this.findService.api_post('users/details', { id: userId }).subscribe({
    //   next: data => { /* preenche modelo */ this.loading = false; },
    //   error: err => { /* trata erro */ this.loading = false; }
    // });
  }
}
