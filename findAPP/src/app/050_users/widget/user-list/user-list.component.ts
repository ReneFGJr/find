import { Component, ElementRef, ViewChild } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { User } from '../../../000_models/user.model';
import { Router } from '@angular/router';


@Component({
  selector: 'app-user-list',
  standalone: false,
  templateUrl: './user-list.component.html',
  styleUrl: './user-list.component.scss',
})
export class UserListComponent {
  users: User[] = [];
  isLoading = false;
  errorMsg: string | null = null;

  /** Referência ao <input> de busca */
  @ViewChild('searchInput', { static: false })
  searchInput!: ElementRef<HTMLInputElement>;
  /** Referência ao <tbody> da tabela */
  @ViewChild('usersTbody', { static: false })
  usersTbody!: ElementRef<HTMLTableSectionElement>;
  /** Referência ao alerta “nenhum resultado” */
  @ViewChild('noResults', { static: false })
  noResults!: ElementRef<HTMLDivElement>;

  constructor(private findService: FindService, private router: Router) {}

  loadUsers(): void {
    this.isLoading = true;
    this.errorMsg = null;
    let dt = {};
    this.findService.api_post('users/list', dt).subscribe({
      next: (data) => {
        this.users = data;
        this.isLoading = false;
      },
      error: (err) => {
        this.errorMsg = 'Não foi possível obter a lista de usuários.';
        this.isLoading = false;
      },
    });
  }

  ngOnInit() {
    this.loadUsers();
  }

  ngAfterViewInit(): void {
    // Só depois que o template existir, registramos o listener
    if (this.searchInput) {
      this.searchInput.nativeElement.addEventListener('keyup', () =>
        this.filterTable()
      );
    }
  }

  viewDetails(user: User): void {
    this.router.navigate(['/users/details', user.id_us]);
  }

  editUser(user: User): void {
    this.router.navigate(['/users/edit', user.id_us]);
  }

  formatDateString(dateStr: string): string {
    return this.findService.formatDateString(dateStr);
  }

  isMember(library: string): boolean {
    return library ? true : false;
  }

  /**
   * “Exclui” logicamente (inativa) o usuário.
   * Após a resposta, recarrega a lista.
   */
  inactivateUser(user: User): void {
    if (!confirm(`Deseja realmente inativar o usuário "${user.us_nome}"?`)) {
      return;
    }

    this.findService
      .api_post('user/inactivate', { login: user.us_login })
      .subscribe({
        next: () => {
          // Recarregar lista após inativação
          this.loadUsers();
        },
        error: (err) => {
          alert('Falha ao inativar o usuário. Por favor, tente novamente.');
        },
      });
  }

  /** Lógica de filtro que antes estava no <script> */
  private filterTable(): void {
    const inputEl = this.searchInput.nativeElement;
    const tbodyEl = this.usersTbody.nativeElement;
    const noResultsEl = this.noResults.nativeElement;

    const filterValue = inputEl.value.trim().toLowerCase();
    const rows = Array.from(tbodyEl.getElementsByTagName('tr'));
    let anyVisible = false;

    rows.forEach((row) => {
      const cols = row.getElementsByTagName('td');
      const nome = cols[0]?.textContent?.trim().toLowerCase() || '';
      const login = cols[1]?.textContent?.trim().toLowerCase() || '';

      if (
        filterValue === '' ||
        nome.includes(filterValue) ||
        login.includes(filterValue)
      ) {
        row.style.display = '';
        anyVisible = true;
      } else {
        row.style.display = 'none';
      }
    });

    // Se nenhuma linha ficou visível, mostra o alerta “Nenhum usuário encontrado”
    if (!anyVisible) {
      noResultsEl.classList.remove('d-none');
    } else {
      noResultsEl.classList.add('d-none');
    }
  }
}
