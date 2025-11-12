import { Component, ElementRef, Input, ViewChild } from '@angular/core';
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
  @Input() idGroup: string = ''; // ID do grupo de usuários, se necessário
  users: User[] = [];
  isLoading = false;
  errorMsg: string | null = null;
  paginatedUsers: any[] = [];

  // Paginação
  itemsPerPage = 50;
  currentPage = 1;
  totalPages = 1;

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

  loadUsers() {
    this.isLoading = true;
    let dt = {
      id_gr: this.idGroup,
      library: localStorage.getItem('library') || '',
    };
    this.findService.api_post('users/list', dt).subscribe({
      next: (data) => {
        this.users = data;
        this.totalPages = Math.ceil(this.users.length / this.itemsPerPage);
        this.updatePaginatedUsers();
        this.isLoading = false;
      },
      error: (err: any) => {
        this.errorMsg = 'Erro ao carregar usuários.';
        this.isLoading = false;
      },
    });
  }

  ngOnInit() {
    console.log('============', this.idGroup);
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

  activateUser(user: User): void {
    if (!confirm(`Deseja realmente ativar o usuário "${user.us_nome}"?`)) {
      return;
    }

    this.findService
      .api_post('users/activate', { userID: user.id_us })
      .subscribe({
        next: (data) => {
          // Recarregar lista após inativação
          console.log(data);
          this.loadUsers();
        },
        error: (err) => {
          alert('Falha ao inativar o usuário. Por favor, tente novamente.');
        },
      });
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
      .api_post('users/inactivate', { userID: user.id_us })
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

  createUser() {
    // Exemplo: navegar para a página de criação
    this.router.navigate(['/users/new']);

    // ou abrir modal:
    // this.openUserFormModal();
  }

  updatePaginatedUsers() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    const end = start + this.itemsPerPage;
    this.paginatedUsers = this.users.slice(start, end);
  }

  nextPage() {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
      this.updatePaginatedUsers();
    }
  }

  previousPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
      this.updatePaginatedUsers();
    }
  }

  filterUsers(term: string) {
    const filtered = this.users.filter((u) =>
      `${u.us_nome} ${u.us_login}`.toLowerCase().includes(term.toLowerCase())
    );
    this.totalPages = Math.ceil(filtered.length / this.itemsPerPage);
    this.currentPage = 1;
    this.paginatedUsers = filtered.slice(0, this.itemsPerPage);
  }
}
