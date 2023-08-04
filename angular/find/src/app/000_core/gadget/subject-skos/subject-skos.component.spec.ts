import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SubjectSkosComponent } from './subject-skos.component';

describe('SubjectSkosComponent', () => {
  let component: SubjectSkosComponent;
  let fixture: ComponentFixture<SubjectSkosComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [SubjectSkosComponent]
    });
    fixture = TestBed.createComponent(SubjectSkosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
