		/*******************************************************************************************************************************
		*******************************************************************************************************************************/
		function createDropdown(optionText, formulaElements) {
		const dropdown = document.createElement('div');
		dropdown.className = 'dropdown ms-2';

		const dropdownButton = document.createElement('button');
		dropdownButton.className = 'btn btn-primary dropdown-toggle';
		dropdownButton.type = 'button';
		dropdownButton.setAttribute('data-bs-toggle', 'dropdown');
		dropdownButton.innerText = `Seleccionar ${optionText}`;

		const dropdownMenu = document.createElement('ul');
		dropdownMenu.className = 'dropdown-menu';

		const items = optionText === 'Operador' ? ['+', '-', '*', '/'] : ['=', '<', '>' , 'AND' , 'OR' ];
		    items.forEach(op=> {
		    const li = document.createElement('li');
		    const button = document.createElement('button');
		    button.className = 'dropdown-item';
		    button.type = 'button';
		    button.innerText = op;
		    button.onclick = () => {
		    formulaElements.push({
		    type: 'operator',
		    value: op
		    });
		    };
		    li.appendChild(button);
		    dropdownMenu.appendChild(li);
		    });

		    dropdown.appendChild(dropdownButton);
		    dropdown.appendChild(dropdownMenu);
		    return dropdown;
		    }