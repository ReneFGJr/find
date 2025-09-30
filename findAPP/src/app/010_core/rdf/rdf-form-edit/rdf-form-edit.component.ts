import { ActivatedRoute, ParamMap } from '@angular/router';
import { Component } from '@angular/core';
import { FindService } from '../../service/find.service';

@Component({
  selector: 'app-rdf-form-edit',
  standalone: false,
  templateUrl: './rdf-form-edit.component.html',
  styleUrl: './rdf-form-edit.component.scss',
})
export class RdfFormEditComponent {
  type: string | null = null;
  data: Array<any> | any;

  constructor(private route: ActivatedRoute,
  private findService: FindService) {}

  ngOnInit(): void {
    this.route.paramMap.subscribe((params: ParamMap) => {
      this.type = params.get('type');
      console.log('Parametro type:', this.type);

      this.findService
        .api_post('form/property', { type: this.type })
        .subscribe((data) => {
          console.log('Dados recebidos:', data);
        });
    })
  }
}
