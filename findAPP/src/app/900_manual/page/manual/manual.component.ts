import { Component } from '@angular/core';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { SocialService } from '../../../010_core/service/social.service';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-manual',
  standalone: false,
  templateUrl: './manual.component.html',
  styleUrl: './manual.component.scss',
})
export class ManualComponent {
  manualContent: Array<any> | any;

  manualInfo = {
    title: 'Manual do Sistema',
    subtitle: 'Guia do Usuário e Documentação Técnica',
    system: 'FindLabs',
    version: 'v1.2.0',
  };

  menu = [
    { label: 'Introdução', section: 1 },
    { label: 'Módulos', section: 2 },
    { label: 'Funcionalidades', section: 3 },
    { label: 'Procedimentos', section: 4 },
    { label: 'Estrutura dos Dados', section: 5 },
    { label: 'FAQ', section: 5 },
  ];

  constructor(
    private route: ActivatedRoute,
    private socialService: SocialService,
    private findService: FindService
  ) {}

  ngOnInit(): void {
    this.route.paramMap.subscribe((params: ParamMap) => {
      let section = params.get('group');
      console.log('Seção solicitada:', section);
      if (section) {
        this.loadSection(section);
      }
    });
  }

  loadSection(section: any) {
    // Simulação – aqui você chamaria a API CI4
    this.findService.api_post('manual/getSection/' + section).subscribe(
      (res) => {
        this.manualContent = res;
        console.log(res)
      }
    )}



  clearContent() {
    this.manualContent = null;
  }
}

export interface ManualSection {
  id: number;
  title: string;
  content: string;
}

export interface Manual {
  title: string;
  version: string;
  audience: string;
  sections: ManualSection[];
}
