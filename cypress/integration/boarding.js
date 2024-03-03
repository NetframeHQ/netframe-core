describe('Boarding', () => {
  it('Redirect to login when visiting the home', () => {
    cy.visit('/');
    cy.location('pathname').should('eq', '/login');

    cy.screenshot('auth-home');

    cy.get('form#auth_login').screenshot('auth-login').within(() => {
      cy.get('input#email');
      cy.get('input#password');
      cy.get('button.login-submit[type="submit"]');

      cy.get('.login-foot').children('a')
        .should('have.attr', 'href')
        .and('match', /\/forgot-password\/?$/);

      cy.get('p').children('a')
        .should('have.attr', 'href')
        .and('match', /\/register\/?$/);
    });
  });

  it('Allow registration', () => {
    cy.get('form#auth_login p a').click();
    cy.location('pathname').should('eq', '/boarding');

    cy.screenshot('boarding-step-1');

    cy.get('form#boarding').within(() => {
      cy.get('input#email').should('have.attr', 'type', 'email');
      cy.get('button').should('have.attr', 'type', 'submit');
    });
  });

  it('Stay at step 1 if no email given', () => {
    cy.get('form#boarding button[type="submit"]').click();
    cy.location('pathname').should('match', /^\/boarding/);
  });

  it('Go to step 2 if email given', () => {
    cy.get('form#boarding input#email').type(`admin@netframe-${Date.now()}.test`);
    cy.get('form#boarding button[type="submit"]').click();
    //cy.location('pathname').should('eq', '/boarding/send-code');
    cy.location('pathname').should('match', /^\/boarding/);
	
	cy.screenshot('boarding-input-code');

    cy.get('form#boarding-checkcode').within(() => {
      cy.get('div.input').children('input')
        .should('have.attr', 'name')
        .and('match', /^n\d/);
      cy.get('button').should('have.attr', 'type', 'submit');
    });
    cy.get('span.boarding-code').then($value => {
        const textValue = $value.text().replace("-", "")
        cy.get('form#boarding-checkcode input[name="n1"]').type(textValue);
        cy.get('form#boarding-checkcode button[type="submit"]').click();
    })
  });
});
