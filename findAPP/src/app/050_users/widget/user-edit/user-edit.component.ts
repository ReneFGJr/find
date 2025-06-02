import { Component, Input, OnInit } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-user-edit',
  standalone: false,
  templateUrl: './user-edit.component.html',
  styleUrl: './user-edit.component.scss',
})
export class UserEditComponent implements OnInit {
  @Input() userID: number = 0;

  /** Armazena todos os campos do usuário retornados pela API */
  user: any = {
    id_us: '',
    us_apikey: '',
    us_nome: 'XXX',
    us_nickname: '',
    us_email: '',
    us_cidade: '',
    us_pais: '',
    us_codigo: '',
    us_badge: '',
    us_link: '',
    us_ativo: '',
    us_nivel: '',
    us_image: '',
    us_genero: '',
    us_verificado: '',
    us_autenticador: '',
    us_cadastro: '',
    us_revisoes: '',
    us_colaboracoes: '',
    us_acessos: '',
    us_pesquisa: '',
    us_erros: '',
    us_outros: '',
    us_last: '',
    us_perfil: '',
    us_login: '',
    us_password: '',
    us_perfil_check: '',
    us_institution: '',
    us_password_method: '',
    us_oauth2: '',
    us_lastaccess: '',
  };

  isLoading = false;
  errorMsg: string | null = null;

  constructor(private findService: FindService, private router: Router) {}

  ngOnInit() {
    if (this.userID > 0) {
      this.loadUserDetails();
    }
  }

  /**
   * Busca detalhes do usuário pela API e preenche `this.user` com os dados retornados.
   */
  loadUserDetails(): void {
    this.isLoading = true;
    this.errorMsg = null;

    const payload = { id: this.userID };
    this.findService.api_post('users/details/' + this.userID).subscribe({
      next: (data: any) => {
        // Supondo que `data` contenha o objeto completo do usuário
        this.user = { ...data };
        this.isLoading = false;
      },
      error: (err) => {
        this.errorMsg = 'Não foi possível carregar os detalhes do usuário.';
        this.isLoading = false;
      },
    });
  }

  /**
   * Envia as alterações do `this.user` para a API.
   * Chamado, por exemplo, ao submeter o formulário de edição.
   */
  saveUser(): void {
    if (!this.user || !this.user.id_us) {
      this.errorMsg = 'Dados de usuário inválidos.';
      return;
    }

    this.isLoading = true;
    this.errorMsg = null;

    // Monta payload com todos os campos modificáveis
    const payload = { ...this.user };
    this.findService.api_post('users/update', payload).subscribe({
      next: (res: any) => {
        this.isLoading = false;
        // Navega de volta para a lista ou rota desejada
        this.router.navigate(['/users/list']);
      },
      error: (err) => {
        this.errorMsg = 'Falha ao atualizar o usuário. Tente novamente.';
        this.isLoading = false;
      },
    });
  }

  /**
   * “Inativa” (remove) o usuário via API. Após sucesso, retorna à lista.
   */
  inactivateUser(): void {
    if (!this.user || !this.user.id_us) {
      this.errorMsg = 'Dados de usuário inválidos.';
      return;
    }

    const confirmed = confirm(
      `Deseja realmente inativar o usuário "${this.user.us_nome}"?`
    );
    if (!confirmed) {
      return;
    }

    this.isLoading = true;
    this.errorMsg = null;

    const payload = { id_us: this.user.id_us };
    this.findService.api_post('users/inactivate', payload).subscribe({
      next: () => {
        this.isLoading = false;
        alert('Usuário inativado com sucesso.');
        this.router.navigate(['/users/list']);
      },
      error: (err) => {
        this.errorMsg = 'Falha ao inativar o usuário.';
        this.isLoading = false;
      },
    });
  }

  /**
   * Cancela a edição e volta para a lista de usuários sem salvar nada.
   */
  cancelEdit(): void {
    this.router.navigate(['/users/list']);
  }

  /**
   * Cancela a edição e volta para a lista de usuários sem salvar nada.
   */
  detailUser(): void {
    this.router.navigate(['/users/details/' + this.user.id_us]);
  }
}
