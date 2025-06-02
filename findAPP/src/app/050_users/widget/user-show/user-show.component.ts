import { Component, Input } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-user-show',
  standalone: false,
  templateUrl: './user-show.component.html',
  styleUrl: './user-show.component.scss',
})
export class UserShowComponent {
  @Input() userID: number = 0;

  /** Objeto que conterá os dados do usuário retornados pela API */
  user: any = null;

  isLoading = false;
  errorMsg: string | null = null;

  constructor(
    private findService: FindService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  private loadUserDetails(): void {
    this.isLoading = true;
    this.errorMsg = null;
    let Url = 'users/details/' + this.userID;
    this.findService.api_post(Url, []).subscribe({
      next: (data: any) => {
        this.user = { ...data };
        this.isLoading = false;
      },
      error: (err) => {
        this.errorMsg = 'Não foi possível carregar os dados do usuário.';
        this.isLoading = false;
      },
    });
  }

  ngOnInit(): void {
    // Se userID não foi passado como @Input, tentamos ler da rota
    if (this.userID === 0) {
      const paramId = this.route.snapshot.paramMap.get('id_us');
      this.userID = paramId ? +paramId : 0;
    }

    if (this.userID > 0) {
      this.loadUserDetails();
    } else {
      this.errorMsg = 'ID de usuário inválido para exibição.';
    }
  }


  editUser(user:any): void {
    this.router.navigate(['/users/edit/'+user.id_us]);
  }

  formatDateString(dateStr: string): string {
    return this.findService.formatDateString(dateStr);
  }

  /** Volta para a lista de usuários */
  goBack(): void {
    this.router.navigate(['/users/list']);
  }
}
